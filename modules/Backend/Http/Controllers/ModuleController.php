<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;
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

    public function index(Request $request, $module)
    {
        $moduleConfig = $this->checkAction($module, 'index');
        $sData = $request->query();
        $sData = array_filter($sData, function ($item) {
            return !empty($item);
        });
        $pageSize = (isset($sData['page_size']) && ($sData['page_size'] > 0) && ($sData['page_size'] <= 50)) ? (int) $sData['page_size'] : 15;
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
        if ($moduleConfig['index']['with']) {
            $with = $moduleConfig['index']['with'];
            $query = $query->with($with);
        }
        if (is_array($moduleConfig['index']['orderBy']) && (count($moduleConfig['index']['orderBy']) == 2)) {
            $orderBy = $moduleConfig['index']['orderBy'];
            $query->orderBy($orderBy[0], $orderBy[1]);
        }
        $moduleItems = $query
            ->orderBy('created_at', 'desc')
            ->paginate($pageSize);
        return [
            'items' => $moduleItems->items(),
            'total' => $moduleItems->total(),
        ];

    }

    public function store(Request $request, $module)
    {
        $moduleConfig = $this->checkAction($module, 'store');
        $model = app($moduleConfig['model']);
        $inputs = $request->all();
        $rules = $model->rules();
        $messages = $model->messages();

        $validator = Validator::make($inputs, $rules, $messages)->validate();
        die();
        if ($validator->fails()) {
            throw new LogicException(LogicException::COMMON_VALIDATION_FAIL, $validator->messages()->first());
        }
        foreach ($inputs as $attr => $val) {
            $model->$attr = e($val);
        }
        $article = new Article;
        $article->title = e($inputs['title']);
        $article->cid = intval($inputs['cid']);
        $article->description = e($inputs['description']);
        $article->content = $inputs['content'];
        $article->slug = $inputs['slug'];
        $article->thumb = empty($inputs['thumb']) ? '' : e($inputs['thumb']);
        $tmp_flag = '';
        /*这里需要对推荐位flag进行处理*/
        if(!empty($inputs['flag']) && is_array($inputs['flag'])) {
            foreach($inputs['flag'] as $flag)
            {
                if(!empty($flag)){
                    $tmp_flag .= $flag.',';
                }
            }
        }
        $article->flag = $tmp_flag;
        if($article->save()) {
            return redirect()->to(site_path('article', 'admin'))->with('message', '成功撰写新文章！');
        } else {
            return redirect()->back()->withInput($request->input())->with('fail', '数据库操作返回异常！');
        }
    }

    public function view($id)
    {
        $article = Article::find($id);
        $categories = Category::all();
        is_null($article) AND abort(404);
        return view('admin.back.article.edit', compact('article', 'categories'));
    }

    public function update(ArticleRequest $request, $id)
    {
        if (Gate::denies('product-write')) {
            throw new AccessDeniedHttpException('拒绝访问该接口');
        }
        $inputs = $request->all();
        $article = Article::find($id);
        $article->title = e($inputs['title']);
        $article->cid = intval($inputs['cid']);
        $article->description = e($inputs['description']);
        $article->content = $inputs['content'];
        $article->slug = $inputs['slug'];
        $article->thumb = empty($inputs['thumb']) ? '' : e($inputs['thumb']);
        $tmp_flag = '';
        /*这里需要对推荐位flag进行处理*/
        if(!empty($inputs['flag']) && is_array($inputs['flag'])) {
            foreach($inputs['flag'] as $flag)
            {
                if(!empty($flag)){
                    $tmp_flag .= $flag.',';
                }
            }
        }
        $article->flag = $tmp_flag;
        if($article->save()) {
            return redirect()->to(site_path('article', 'admin'))->with('message', '成功更新文章！');
        } else {
            return redirect()->back()->withInput($request->input())->with('fail', '数据库操作返回异常！');
        }
    }

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
        if (!in_array($action, $actions)) {
            throw new NotFoundHttpException('404 Not Found!');
        }
        $can = isset($moduleConfig['can']) ? $moduleConfig['can'] : null;
        if (!isset($moduleConfig[$action])) {
            throw new NotFoundHttpException('404 Not Found!');
        }
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