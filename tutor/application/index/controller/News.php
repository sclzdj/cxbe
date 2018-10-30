<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\project\model\Category as CategoryModel;
use app\project\model\News as NewsModel;
use think\Db;
use think\Request;

/**
 * 前台新闻控制器
 * @package app\index\controller
 */
class News extends Base
{
    protected $_pageSize=10;
    /**
     * 新闻列表
     * @return mixed
     */
    public function index($category_id='')
    {
        $page_title='新闻列表';
        $map=[];
        $where='';
        $lastItem=Request::instance()->post('lastItem/a');
        if($lastItem!==null){
            $where=" (create_time < {$lastItem['create_time']} || (create_time = {$lastItem['create_time']} && id > {$lastItem['id']}))";
        }
        if($category_id!=''){
            $category=CategoryModel::find($category_id);
            if(!$category){
                if(Request::instance()->isAjax()){
                    return $this->_error('请求错误');
                }else{
                    return $this->error('请求错误');
                }
            }
            $category_ids=array_merge((array)$category_id,CategoryModel::getChildsId($category_id));
            $map['category_id']=['in',$category_ids];
        }
        $news=NewsModel::fieldLimit('content')->where(['status'=>1])->where($where)->where($map)->order('create_time desc')->limit($this->_pageSize)->select();
        $categorys=CategoryModel::fieldLimit()->where(['pid'=>0])->order('sort asc,id asc')->select();
        if(Request::instance()->isAjax()){
            foreach ($news as $k=>$v){
                $news[$k]['news_url']=url('index/news/read',['id'=>$v['id']]);
                $news[$k]['pic_url']=get_file_path($v['pic']);
                $news[$k]['create_time_str']=date('Y-m-d',$v['create_time']);
            }
            return $this->_success($news);
        }else{
            return view('mobile/news/index',compact('page_title','news','categorys','category_id'));
        }

    }
    /**
     * 新闻内容
     * @return mixed
     */
    public function read($id='')
    {
        $page_title='新闻详情';
        $news=NewsModel::fieldLimit()->where(['status'=>1,'id'=>$id])->find();
        if(!Request::instance()->isAjax() && !$news){
            return $this->error('请求错误');
        }
        $where='';
        $lastItem=Request::instance()->post('lastItem/a');
        if($lastItem!==null){
            $where=" (create_time < {$lastItem['create_time']} || (create_time = {$lastItem['create_time']} && id > {$lastItem['id']}))";
        }
        $hot_recomment_news=NewsModel::fieldLimit('content')->where(['status'=>1,'is_hot_recomment'=>1,'id'=>['neq',$id]])->where($where)->order('create_time desc')->limit($this->_pageSize)->select();
        if(Request::instance()->isAjax()){
            foreach ($hot_recomment_news as $k=>$v){
                $hot_recomment_news[$k]['news_url']=url('index/news/read',['id'=>$v['id']]);
                $hot_recomment_news[$k]['pic_url']=get_file_path($v['pic']);
                $hot_recomment_news[$k]['create_time_str']=date('Y-m-d',$v['create_time']);
            }
            return $this->_success($hot_recomment_news);
        }else{
            return view('mobile/news/read',compact('page_title','news','hot_recomment_news'));
        }
    }
}
