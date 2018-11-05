<?php

namespace Modules\Backend\Http\Controllers;


use Illuminate\Http\Request;
use Modules\Common\Exception\LogicException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;


class ArticleController extends BaseController
{

    public function __construct()
    {
        parent::__construct();

    }

    public function index(Request $request)
    {
        $s_title = $request->input('s_title');
        $s_cid = $request->input('s_cid');

        $categories = DB::table('categories')->get();
        $articles = DB::table('articles')->where('title', 'like', '%'.$s_title.'%')
            ->where('cid', (($s_cid > 0) ? '=' : '<>'), $s_cid)
            ->orderBy('created_at','desc')
            ->paginate(2);
        $flags = config('ecms.flag.articles');
        // return compact('categories', 'articles', 'flags');
        return [
            'items' => $articles->items(),
            'total' => $articles->total(),
        ];
    }

    public function store(Request $request)
    {
        if (Gate::denies('product-write')) {
            throw new AccessDeniedHttpException('拒绝访问该接口');
        }
        $inputs = $request->all();
        echo 'hello';
        die();
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
}