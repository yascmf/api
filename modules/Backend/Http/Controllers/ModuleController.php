<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;


class ModuleController extends BaseController
{
    protected $modules = [];
    protected $routers = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->routers = config('yascmf.routers');
        $this->modules = config('yascmf.modules');
    }
    
    public function render(Request $request, $module)
    {
        
    }

    /**
     * index
     *
     * @param Request $request
     * @param string $module
     * @return array
     */
    public function index(Request $request, $module)
    {
        $moduleConfig = $this->checkAction($module, 'index');
        $sData = $request->query();
        $sData = array_filter($sData, function ($item) {
            return !empty($item);
        });
        $pageSize = (isset($sData['page_size']) && ($sData['page_size'] > 0) && ($sData['page_size'] <= 150)) ? (int) $sData['page_size'] : 15;
        $query = app($moduleConfig['model']);
        if (is_array($moduleConfig['index']['filters']) && (count($moduleConfig['index']['filters']) > 0)) {
            $filters = $moduleConfig['index']['filters'];
            $query = $query->where(function ($query) use ($sData, $filters) {
                    foreach ($filters as $sKey => $where) {
                        if (is_array($where) && isset($sData[$sKey])) {
                            switch (count($where)) {
                                case 2:
                                    $fieldValue = str_replace('{fieldValue}', $sData[$sKey], $where[1]);
                                    $query->where($where[0], $fieldValue);
                                    break;
                                case 3:
                                    $fieldValue = str_replace('{fieldValue}', $sData[$sKey], $where[2]);
                                    $query->where($where[0], $where[1], $fieldValue);
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                });
        }
        if (isset($moduleConfig['index']['with']) && is_string($moduleConfig['index']['with']) && !empty($moduleConfig['index']['with'])) {
            $with = $moduleConfig['index']['with'];
            $query = $query->with($with);
        }
        if (is_array($moduleConfig['index']['orderBy']) && (count($moduleConfig['index']['orderBy']) == 2)) {
            $orderBy = $moduleConfig['index']['orderBy'];
            if ($orderBy[0] !== 'id') {
                $query = $query->orderBy($orderBy[0], $orderBy[1])
                    ->orderBy('id', 'desc');
            } else {
                $query = $query->orderBy($orderBy[0], $orderBy[1]);
            }
        }
        $moduleItems = $query
            ->paginate($pageSize);
        return [
            'items' => $moduleItems->items(),
            'total' => $moduleItems->total(),
        ];

    }

    /**
     * store
     *
     * @param Request $request
     * @param string $module
     * @return array
     * @throws LogicException
     */
    public function store(Request $request, $module)
    {
        $moduleConfig = $this->checkAction($module, 'store');
        $model = app($moduleConfig['model']);
        $inputs = $request->except(['id', 'created_at', 'updated_at', 'x-request-id']);
        $rules = $model->rules();
        $messages = $model->messages();
        $validator = Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages()->first();
            throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, $messages);
        }
        if (method_exists($model, 'beforeSaving')) {
            $inputs = $model->beforeSaving($request);
        }
        foreach ($inputs as $attr => $val) {
            $model->$attr = $val;
        }
        if ($model->save()) {
            if (method_exists($model, 'afterSaving')) {
                $model->afterSaving($model, $request);
            }
            return [
                'code' => LogicException::COMMON_SUCCESS,
                'message' => 'ok',
            ];
        } else {
            throw new LogicException(LogicException::COMMON_DB_SAVE_FAIL);
        }
    }

    /**
     * show
     *
     * @param Request $request
     * @param string $module
     * @param int $id
     * @return array
     */
    public function show(Request $request, $module, $id)
    {
        $moduleConfig = $this->checkAction($module, 'store');
        $query = app($moduleConfig['model']);
        if (isset($moduleConfig['show']['with']) && is_string($moduleConfig['show']['with']) && !empty($moduleConfig['show']['with'])) {
            $with = $moduleConfig['show']['with'];
            $query = $query->with($with);
        }
        $model = $query->find($id);
        return $model->toArray();
    }

    /**
     * update
     *
     * @param Request $request
     * @param string $module
     * @param int $id
     * @return array
     * @throws LogicException
     */
    public function update(Request $request, $module, $id)
    {
        $moduleConfig = $this->checkAction($module, 'store');
        $model = app($moduleConfig['model']);
        $model = $model->find($id);
        $inputs = $request->except(['id', 'created_at', 'updated_at', 'x-request-id']);
        $rules = $model->rules(['id' => $id]);
        $messages = $model->messages();
        $validator = Validator::make($inputs, $rules, $messages);
        if ($validator->fails()) {
            $messages = $validator->messages()->first();
            throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, $messages);
        }
        if (method_exists($model, 'beforeSaving')) {
            $inputs = $model->beforeSaving($request);
        }
        foreach ($inputs as $attr => $val) {
            $model->$attr = $val;
        }
        if ($model->save()) {
            if (method_exists($model, 'afterSaving')) {
                $model->afterSaving($model, $request);
            }
            return [
                'code' => LogicException::COMMON_SUCCESS,
                'message' => 'ok',
            ];
        } else {
            throw new LogicException(LogicException::COMMON_DB_SAVE_FAIL);
        }
    }

    /**
     * checkAction
     * 
     * @param string $module
     * @param string $action
     * @return mixed
     */
    private function checkAction($module, $action = 'index')
    {
        if (!in_array($module, $this->routers)) {
            throw new NotFoundHttpException('404 Not Found!');
        }
        if (isset($this->modules[$module])) {
            $moduleConfig = $this->modules[$module];
        } else {
            throw new NotFoundHttpException('404 Not Found!');
        }
        $actions = explode(',', $moduleConfig['actions']);
        if (!in_array($action, $actions) || !isset($moduleConfig[$action])) {
            throw new NotFoundHttpException('404 Not Found!');
        }
        $can = isset($moduleConfig['can']) ? $moduleConfig['can'] : null;
        $actionCan = isset($moduleConfig[$action]['can']) ? $moduleConfig[$action]['can'] : null;
        if (is_string($can) && !empty($can)) {
            if (Gate::denies($can)) {
                throw new AccessDeniedHttpException('This action is unauthorized.');
            }
        }
        if (is_string($actionCan) && !empty($actionCan)) {
            if (Gate::denies($actionCan)) {
                throw new AccessDeniedHttpException('This action is unauthorized.');
            }
        }
        return $moduleConfig;
    }
}