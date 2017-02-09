<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-01-21
* 版    本：1.0.0
* 功能说明：前台控制器演示。
*
**/
namespace Home\Controller;
use Think\Controller;
use Org\Util ;
use Think\Think;

class UserController extends ComController
{


    public function index()
    {

        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/index/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        if ($_SESSION['userinfo']['jibie'] == 0) {
            $jibie = '测试会员';
        } elseif ($_SESSION['userinfo']['jibie'] == 1) {
            $jibie = '测试会员2';
        } elseif ($_SESSION['userinfo']['jibie'] == 2) {
            $jibie = '测试会员3';
        } elseif ($_SESSION['userinfo']['jibie'] == 3) {
            $jibie = '测试会员4';
        }

        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;


        $map['addtime'] = array('between', array($beginToday, $endToday));
        $jinritongji = M('user')->where($map)->count();

        $this->assign('jinritongji', $jinritongji);

        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $result['addtime'] = array('between', array($beginYesterday, $endYesterday));

        $zuoritongji = M('user')->where($result)->count();
        //var_dump($zuoritongji);
        //月份时间戳
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $endThismonth = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        $yue['kaihutime'] = array('between', array($beginThismonth, $endThismonth));
        $yue2['cskaihutime'] = array('between', array($beginThismonth, $endThismonth));
        $byzc1 = M('user')->where($yue)->Sum('lnxxzc');//当月的信息资产统计和
        $byzc2 = M('user')->where($yue2)->Sum('csxxzc');
        $byzc = $byzc1 + $byzc2;
        $fenhong = $byzc / 10;//平台当月分红的金额
        $this->assign('fenhong', $fenhong);
       /* $lnzl = M('lnzl')->count('dengluzhanghao');
        $cszl = M('cszl')->count('dengluzhanghao');
        $yunjigu = $lnzl + $cszl;//云集谷人数*/
        $where['shouxu']=array('gt',0);
        $yunjigu=M('user')->where($where)->count();
        $zongzc1 = $user->sum('lnxxzc');//信息资产总量
        $zongzc2 = $user->sum('csxxzc');
        $zongzc = $zongzc1 + $zongzc2;
        $this->assign('zongzc', $zongzc);
        $this->assign('yunjigu', $yunjigu);
        $this->assign('zuoritongji', $zuoritongji);

        $alltongji = M('user')->count();
        $this->assign('alltongji', $alltongji);

        $this->assign('jibie', $jibie);
        $this->display();


    }

    /*
     * 信息资产详情页面
     * */
    public function user_xxzc()
    {
        $openid = $_GET['openid'];
        $user = M('user');
        $zongzc1 = $user->sum('lnxxzc');//信息资产总量
        $zongzc2 = $user->sum('csxxzc');
        $zongzc = $zongzc1 + $zongzc2;
        //var_dump($zongzc);
        $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $endThismonth = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        $yue['kaihutime'] = array('between', array($beginThismonth, $endThismonth));
        $yue2['cskaihutime'] = array('between', array($beginThismonth, $endThismonth));
        $byzc1 = M('user')->where($yue)->Sum('lnxxzc');//当月的信息资产统计和
        $byzc2 = M('user')->where($yue2)->Sum('csxxzc');
        $byzc = $byzc1 + $byzc2;
        $fenhong = $byzc / 10;//平台当月分红的金额
        /*$lnzl = M('lnzl')->count();
        $cszl = M('cszl')->count();
        $yunjigu = $lnzl + $cszl;//云集谷人数*/
        $where['shouxu']=array('gt',0);
        $yunjigu=M('user')->where($where)->count();
        //$wozc=$user->where(array('openid'=>$openid))->field('xxzc')->find();
        $m = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); //上个月的开始日期
        $t = date('t', strtotime($m)); //上个月共多少天
        $start = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, 1, date('Y'))); //上个月的开始日期
        $end = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, $t, date('Y'))); //上个月的结束日期
        $startMonth = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $endMonth = mktime(0, 0, 0, date('m') - 1, $t, date('Y'));
        $where['kaihutime'] = array('between', array($startMonth, $endMonth));

        $where['openid'] = $openid;
        $where1['cskaihutime'] = array('between', array($startMonth, $endMonth));
        $where1['openid'] = $openid;
        $syzc1 = M('user')->where($where)->sum('lnxxzc');//上月的信息资产总量
        $syzc2 = M('user')->where($where1)->sum('csxxzc');
        $syzc = $syzc1 + $syzc2;
        //var_dump($syzc);
        $syfh = $syzc / 10;
        //var_dump($syfh);
        $userarr = $user->where(array('openid' => $openid))->find();//我的信息
        $jinbi = $userarr['jinbi'];
        $wozc1 = $userarr['lnxxzc'];
        $wozc2 = $userarr['csxxzc'];
        $wozc = $wozc1 + $wozc2;
        //var_dump($jinbi);
        //var_dump($wozc);
        $this->assign('zongzc', $zongzc);
        $this->assign('syfh', $syfh);
        $this->assign('fenhong', $fenhong);
        $this->assign('yunjigu', $yunjigu);
        $this->assign('wozc', $wozc);
        $this->assign('jinbi', $jinbi);

        $this->display();

    }

    /*
     * 信息资产排行*/
    public function zcph()
    {
        header("Content-type:text/html;charset=utf-8");
        $user = M('user');
        $openid=$_GET['openid'];
        //$user->where(array('openid'=>$openid))->find();
        //var_dump($user->order('xxzc desc')->limit(5)->select());
        $page = intval($_GET['page'])? intval($_GET['page']):0;

        $start = $page*10;
        $where['xxzcstatus']=1;
        $list=$user->where($where)->order('xxzc desc')->limit($start,10)->select();
        $this->assign('list',$list);
        //$html = $this->fetch('zclist');
        //echo($html);
        $this->display();


    }

    /*
     * 信息资产排行*/
    public function ajaxGetShops()
    {
        header("Content-type:text/html;charset=utf-8");
        $user = M('user');
        $openid=$_GET['openid'];
        $page = intval($_GET['page'])? intval($_GET['page']):0;

        $start = $page*10;
        $where['xxzcstatus']=1;
        $list=$user->where($where)->order('xxzc desc')->limit($start,10)->select();
        $this->assign('list',$list);
        $html = $this->fetch('zclist');
        echo($html);
        //$this->display();


    }

    /*
     * 信息资产明细*/
    public function zcmx()
    {
        $this->display();

    }

    /*分红明细*/
    public function fhmx()
    {
        $this->display();

    }

    public function kaihupanduan()
    {
        $openid=$_GET['openid'];
        $userarr=M('user')->where(array('openid'=>$openid))->find();
        $tel=$userarr['tel'];

        if($tel){
            $this->display();
        }else{
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }




    }

    /*判断用户关系*/
    public function shengji()
    {
        $user = D('user');
        $openid = $_SESSION['userinfo']['openid'];//获取到用户的身份信息 匹配交易数据获取到用户的开户账号
        $card = $user->where(array('openid' => $openid))->field('cart')->find();
        //var_dump($card);
        $ln = M('lnzl');
        $userarr = $ln->where(array('card' => $card['cart']))->find();
        /*var_dump($result);
        exit;*/
        $data['ln_dengluzhanghao'] = $userarr['dengluzhanghao'];
        $lnresult = $user->where(array('openid' => $openid))->save($data['ln_dengluzhanghao']);
        $cs = M('cszl');
        $csuserarr = $cs->where(array('card' => $card['cart']))->find();
        /*var_dump($result);
        exit;*/
        $data['cs_dengluzhanghao'] = $csuserarr['dengluzhanghao'];
        $csresult = $user->where(array('openid' => $openid))->save($data['cs_dengluzhanghao']);

    }

    /*
     * 获取到交易账号  匹配交易信息 */
    public function lnjiaoyi()
    {
        $user = M('user');
        $openid = $_SESSION['userinfo']['openid'];
        //var_dump($openid);
        $usercard = $user->where(array('openid' => $openid))->find();
        var_dump($usercard);
        $userzh = M('lnzl')->where(array('card' => $usercard["cart"]))->field('dengluzhanghao')->find();
        //var_dump(array('card'=>$usercard["cart"]));
        var_dump($userzh);

    }


    /**
     *
     *
     * @param 上级升级操作方法
     *
     * $id 用户id
     */
    public function shangjisj($id)
    {
        $user = D('user');

        $userarr = $user->where(array('id' => $id))->find();//用户所有信息数组

        $suserarr = $user->where(array('id' => $userarr['sid']))->find();//上级用户所有信息数组


        $sjibie = $suserarr['jibie'];//上级级别
        $where['jibie'] = $sjibie - 1;//直属下级级别
        $where['sid'] = array('in', $userarr['sid']);
        $count = $user->where($where)->count();//所有直属下级数组
        if ($count > 2 && $userarr['jibie'] !== 1) {
            $user->where(array('id' => $userarr['sid']))->save(array('jibie' => $suserarr['jibie'] + 1));
        } elseif ($count > 0 && $userarr['jibie'] == 1) {
            $user->where(array('id' => $userarr['sid']))->save(array('jibie' => $suserarr['jibie'] + 1));
            //
        }
    }

    /**
     *
     *
     * @param 分佣方法
     */
    public function fenyong($yongjin, $id, $jiaoyiid)
    {
        $user = D('user');
        $yongjinjilu = D('yongjinjilu');


        $userarr = $user->where(array('id' => $id))->find();//用户所有信息数组
        // $ujibie = $userarr['jibie'];
        //极端情况
        if ($userarr['sid'] > 1 && $userarr['jibie'] > 5) {

        } elseif ($userarr['sid'] > 1 && $userarr['jibie'] = 5) {
            $suserarr = $user->where(array('id' => $userarr['sid']))->find();
            $yongjinjilu->add(array('jiner' => $this->jisuanyj($yongjin, $suserarr['jibie']), 'userid' => $suserarr['id'], 'time' => time(), 'jiaoyiid' => $jiaoyiid));
        } else {
            if ($userarr['sid'] > 1 && $userarr['jibie'] < 5) {
                $suserarr = $user->where(array('id' => $userarr['sid']))->find();
                $yongjinjilu->add(array('jiner' => $this->jisuanyj($yongjin, $suserarr['jibie']), 'userid' => $suserarr['id'], 'time' => time(), 'jiaoyiid' => $jiaoyiid));
                if ($suserarr['sid'] > 1 && $suserarr['jibie'] < 5) {
                    $ssuserarr = $user->where(array('id' => $suserarr['sid']))->find();
                    $yongjinjilu->add(array('jiner' => $this->jisuanyj($yongjin, $suserarr['jibie']), 'userid' => $ssuserarr['id'], 'time' => time(), 'jiaoyiid' => $jiaoyiid));
                    if ($ssuserarr['sid'] > 1 && $ssuserarr['jibie'] < 5) {
                        $sssuserarr = $user->where(array('id' => $ssuserarr['sid']))->find();
                        $yongjinjilu->add(array('jiner' => $this->jisuanyj($yongjin, $suserarr['jibie']), 'userid' => $sssuserarr['id'], 'time' => time(), 'jiaoyiid' => $jiaoyiid));
                        if ($sssuserarr['sid'] > 1 && $sssuserarr['jibie'] < 5) {
                            $ssssuserarr = $user->where(array('id' => $sssuserarr['sid']))->find();
                            $yongjinjilu->add(array('jiner' => $this->jisuanyj($yongjin, $suserarr['jibie']), 'userid' => $ssssuserarr['id'], 'time' => time(), 'jiaoyiid' => $jiaoyiid));
                        }
                    }
                }
            }
        }


        //如果有上级且上级不为系统  则分配佣金入库
        //如果没有上级了


    }

    public function jisuanyj($jibie, $yongjin)
    {
        if ($jibie == 1) {
            return $yongjin * 0.2;
        } elseif ($jibie == 2) {
            return $yongjin * 0.25;
        } elseif ($jibie == 3) {
            return $yongjin * 0.3;
        } elseif ($jibie == 4) {
            return $yongjin * 0.4;
        } elseif ($jibie == 5) {
            return $yongjin * 0.45;
        }

    }
    // 获取access_token 两小时有效
    private function get_access_token(){
        $appid ="wx28518b7b4a4dba5d";
        $appsecret ="edae2ddedfd2b5dde7f7d59df67fde0a";
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
        $rurl = file_get_contents($url);
        $rurl = json_decode($rurl,true);
        if(array_key_exists('errcode',$rurl)){
            return false;
        }else{
            $access_token = $rurl['access_token'];
            return $access_token;
        }
    }
    // 获取jsticket 两小时有效
    private function getjsticket(){ // 只允许本类调用，继承的都不可以调用，公开调用就更不可以了
        $access_token = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi"; // 两小时有效
        $rurl = file_get_contents($url);
        $rurl = json_decode($rurl,true);
        if($rurl['errcode'] != 0){
            return false;
        }else{
            $jsticket = $rurl['ticket'];
            return $jsticket;
        }
    }
    // 获取 signature
    private function getsignature(){
        $noncestr = '';
        $jsapi_ticket = $this->getjsticket();
        $timestamp = time();
        $url = 'http://zhudianbao.diandodo.com/index.php?g=Opener&m=Merchant&a=open';
        $string1 = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'×tamp='.$timestamp.'&url='.$url;
        $signature = sha1($string1);
        return $signature;
    }

    /*
     * 申请开户
     * */
    public function dlztc(){


            $openid = $_GET['openid'];
            $ncce = M('nccekaihu');
            $ncceresult = $ncce->where(array('openid' => $openid))->find();
            if ($ncceresult) {
                $this->error('您已经提交申请开户，请勿重复申请');
            } else {

                $appid='wx28518b7b4a4dba5d';
                $appSerect='edae2ddedfd2b5dde7f7d59df67fde0a';
                $jssdk=new Util\Jssdk($appid,$appSerect);

               $signature=$jssdk->GetSignPackage();
                $this->assign('signature',$signature);
                $this->assign('openid', $openid);
                $this->display();
            }


        }


    //申请代理
    public function csdlztc()
    {

        $openid = $_GET['openid'];
        $ncce = M('cskaihu');
        $ncceresult = $ncce->where(array('openid' => $openid))->find();
        if ($ncceresult) {
            //$this->redirect('您已经提交申请开户，请勿重复申请');
            $this->error('您已经提交申请开户，请勿重复申请');
        } else {

            $this->assign('openid', $openid);
            $this->display();
        }


    }

    public function shenqingdaili()
    {
        //var_dump($_GET);
        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/shenqingdaili/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        //$id = $_GET['id'];
       /* $userarr = M('user')->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
        $_SESSION['userinfo'] = $userarr;*/
        $this->assign('list',$chaxunarr);

        //$this->assign($_SESSION);

        $this->display();

    }

    /*
     * 进入人脉详情页面
     * */
    public function rm_index()
    {
        /*$user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/index/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }*/
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;

//		$map['addtime']=array('egt',$beginToday);
//		$map['addtime']=array('elt',$endToday);
        $map['addtime'] = array('between', array($beginToday, $endToday));
        $jinritongji = M('user')->where($map)->count();
        //var_dump($jinritongji);
        $this->assign('jinritongji', $jinritongji);
        /* $result['addtime']=array('egt','1463846400');
        $result['addtime']=array('elt','1464932799');  */
        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $result['addtime'] = array('between', array($beginYesterday, $endYesterday));

        $zuoritongji = M('user')->where($result)->count();
        //var_dump($zuoritongji);
        $this->assign('zuoritongji', $zuoritongji);

        $alltongji = M('user')->count();
        $this->assign('alltongji', $alltongji);
        $id = $_GET['id'];
        $where['id'] = $id;
        $user = M('user');
        $myxj = $user->where($where)->find();
        /* if($myxj!=""){
            foreach ($myxj as $k=>$value){
                $myxj[$k]['jibie']=$this->daili($value['jibie']);
            }*/

        $this->assign('myxj', $myxj);
        //var_dump($myxj);
        $this->display();

    }

    //体验金的判断
    public function tiyanjin()
    {
        $openid = $_GET['openid'];
       $tel= M('user')->where(array('openid'=>$openid))->getField('tel');
        if($tel){
            $userarr = M('user')->where(array('openid' => $openid))->find();
            $this->assign('userarr', $userarr);

            $this->display();
        }else{
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }

    }

    /*
     * 体验金赔付*/
    public function tyj()
    {
        $id = $_GET['id'];
        $user = M('user');
        $userarr = $user->where(array('id' => $id))->find();
        //$kui=$jiaoyishuju->where(array('zhanghao'=>$userarr['ln_dengluzhanghao']))->find();
        //$peifu=$kui['jsyk'];
        $start = $userarr['kaihutime'];
        if(empty($start)){
            $this->display('ktyj');
        }else{
            $starttime = date('Y-m-d', $start);  //第二步



            $endtime = date("Y-m-d", strtotime("+1months", strtotime($starttime)));
            //var_dump($endtime);

            $peifu = $this->kui($userarr['id']);
            //var_dump($peifu);
            $this->assign('starttime', $starttime);
            $this->assign('userarr',$userarr);
            $this->assign('endtime', $endtime);
            $this->assign('peifu', $peifu);
            $this->display();
        }




    }
    /*体验金页面*/
    public function yanjin(){
        $openid = $_GET['openid'];
        $tel= M('user')->where(array('openid'=>$openid))->getField('tel');
        if($tel){
            $userarr = M('user')->where(array('openid' => $openid))->find();
            $this->assign('userarr', $userarr);

            $this->display();
        }else{
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }
    }
    /*黄金页面*/
    public function huangjin(){
        $openid = $_GET['openid'];
        $tel= M('user')->where(array('openid'=>$openid))->getField('tel');
        if($tel){
            $userarr = M('user')->where(array('openid' => $openid))->find();
            $this->assign('userarr', $userarr);

            $this->display();
        }else{
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }
    }
    /*邮币卡页面*/
    public function youbi(){
        $openid = $_GET['openid'];
        $tel= M('user')->where(array('openid'=>$openid))->getField('tel');
        if($tel){
            $userarr = M('user')->where(array('openid' => $openid))->find();
            $this->assign('userarr', $userarr);

            $this->display();
        }else{
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }
    }

    public function jstyj(){
        $wen = M('article');
        $tyj = $wen->where(array('sid'=>19,'aid'=>54))->find();
        $this->assign('tyj', $tyj);
        $this->display();
    }
    public function jlyj(){
        $id=$_GET['id'];
        $page = intval($_GET['page'])? intval($_GET['page']):0;

        $start = $page*10;
        $yongjin=M('yongjinjilu')->where(array('userid'=>$id))->order('time desc')->limit($start,10)->select();
        $csyongjin=M('csyongjinjilu')->where(array('userid'=>$id))->order('time desc')->limit($start,10)->select();
        $this->assign('csyongjin',$csyongjin);
        $this->assign('yongjin',$yongjin);
        $this->display();
    }
    public function txyj(){
        $openid=$_GET['openid'];
        $page = intval($_GET['page'])? intval($_GET['page']):0;
        $start = $page*10;
        $tixian=M('tixian')->where(array('openid'=>$openid))->order('addtime desc')->limit($start,10)->select();
        $this->assign('tixian',$tixian);
        $this->display();

    }

    public function kui($id)
    {
        $user = M('user');
        $jiaoyishuju = M('jiaoyishuju');
        $userarr = $user->where(array('id' => $id))->find();
        $start = $userarr['kaihutime'];
        $end = strtotime("+1 months", $start);
        //$na_members  = time();//测试数据
        //date("Y-m-d",strtotime("+1months",strtotime("2011-08-04")));

        $starttime = date('Y-m-d', $start);  //第二步
        //var_dump($starttime);

        $endtime = date("Y-m-d", strtotime("+1months", strtotime($starttime)));
        $where['addtime'] = array('between', array($start, $end));
        //$where['jsyk'] = array('lt', 0);
        $where['zhanghao'] = $userarr['ln_dengluzhanghao'];
        $kui1 = $jiaoyishuju->where($where)->sum('jsyk');
        $kui2=$jiaoyishuju->where($where)->sum('pcyk');
        $kui=$kui1+$kui2;
        if (-$kui > 1088) {
            $kui = 1088;
            return $kui;
        } else {
            return -$kui;
        }
    }
    /*信息资产的判断*/

    /*
     * 信息资产的计算*/
    public function jisuanzc($jibie)
    {
        //if($jibie==)
    }

    public function edit()
    {
        $user = M('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/edit/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $user=M('user');
        $userarr=$user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
        $this->assign('userarr',$userarr);
        $dizhi = M('dizhi');
        $maxid = $dizhi->where(array('openid' => $_SESSION['userinfo']['openid']))->max('id');
        $dizhiarr = $dizhi->where(array('id' => $maxid))->find();
        $this->assign('list', $dizhiarr);

        $this->display();

    }

    //将用户提交的开户信息写入数据库
    public function ncceds()
    {
        /*var_dump($_POST);
        exit;*/
        //$data=I('post.');
        $openid = $_GET['openid'];
        $user = M('user');
        $userarr = $user->where(array('openid' => $openid))->field('nickname,headimgurl,openid,sid')->find();
        /* $userarr=$user->where(array('openid'=>$openid))->find();
        var_dump($userarr);
        exit;*/
        $data['nickname'] = $userarr['nickname'];
        $data['headimgurl'] = $userarr['headimgurl'];
        $data['openid'] = $userarr['openid'];
        $data['sid'] = $userarr['sid'];
        $data['addtime'] = time();
        //var_dump($data);
        //var_dump($kaihu);
        $data['id'] = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        // $data['cart'] = isset($_POST['cart'])?trim($_POST['cart']):'';
        //$data['weixinhao'] = isset($_POST['weixinhao'])?trim($_POST['weixinhao']):'';
        $data['tel'] = isset($_POST['tel']) ? trim($_POST['tel']) : '';
        $data['kaihuhang'] = isset($_POST['kaihuhang']) ? trim($_POST['kaihuhang']) : '';
        $data['zyinhangka'] = isset($_POST['zyinhangka']) ? trim($_POST['zyinhangka']) : '';
        $data['fyinhangka'] = isset($_POST['fyinhangka']) ? trim($_POST['fyinhangka']) : '';
        $data['yinhang'] = isset($_POST['yinhang']) ? trim($_POST['yinhang']) : '';
        $data['card'] = isset($_POST['card']) ? trim($_POST['card']) : '';
        $data['scard'] = isset($_POST['scard']) ? trim($_POST['scard']) : '';
        $data['zcard'] = isset($_POST['zcard']) ? trim($_POST['zcard']) : '';
        $data['fcard'] = isset($_POST['fcard']) ? trim($_POST['fcard']) : '';


        //$data['yinh']
        // $data['diqu'] = isset($_POST['diqu'])?$_POST['diqu']:'';
        //$data['xiangxi'] = isset($_POST['xiangxi'])?$_POST['xiangxi']:'';
        /*$data['name']&&$data['tel']&&$data['yinhangka']&&$data['zcard']&&$data['fcard']*/
        if ($data['zyinhangka'] && $data['name'] && $data['tel'] && $data['kaihuhang'] && $data['zcard'] && $data['fcard'] && $data['fyinhangka'] && $data['yinhang'] && $data['card'] && $data['scard']) {

            // echo '111';

            $kaihu = M('nccekaihu');

            //var_dump($data);
            $hangshu = $kaihu->add($data);
            $card['cart'] = $data['card'];
            $user->where(array('openid' => $openid))->save($card);


            if ($hangshu) {
                $this->redirect('Home/User/tishi');
                return;


            } else {
                $this->error('<h1>失败,请检查填写内容</h1>');
            }
            return;
        } else {
            $this->error('<h1>您有未填写的选项,请填写完毕,谢谢!</h1>');
        }


    }

    public function csds()
    {
        //var_dump($_POST);
        //exit;
        //$data=I('post.');
        $openid = $_GET['openid'];
        $user = M('user');
        $userarr = $user->where(array('openid' => $openid))->field('nickname,headimgurl,openid')->find();
        $data['nickname'] = $userarr['nickname'];
        $data['headimgurl'] = $userarr['headimgurl'];
        $data['openid'] = $userarr['openid'];
        $data['addtime'] = time();
        //var_dump($data);
        //var_dump($kaihu);
        $data['id'] = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        // $data['cart'] = isset($_POST['cart'])?trim($_POST['cart']):'';
        //$data['weixinhao'] = isset($_POST['weixinhao'])?trim($_POST['weixinhao']):'';
        $data['tel'] = isset($_POST['tel']) ? trim($_POST['tel']) : '';
        $data['yinhang'] = isset($_POST['yinhang']) ? trim($_POST['yinhang']) : '';
        $data['card'] = isset($_POST['card']) ? trim($_POST['card']) : '';
        $data['yinhangka'] = isset($_POST['yinhangka']) ? trim($_POST['yinhangka']) : '';
        $data['zcard'] = isset($_POST['zcard']) ? trim($_POST['zcard']) : '';
        $data['fcard'] = isset($_POST['fcard']) ? trim($_POST['fcard']) : '';
        //$data['yinh']
        // $data['diqu'] = isset($_POST['diqu'])?$_POST['diqu']:'';
        //$data['xiangxi'] = isset($_POST['xiangxi'])?$_POST['xiangxi']:'';
        /*$data['name']&&$data['tel']&&$data['yinhangka']&&$data['zcard']&&$data['fcard']*/
        if ($data['yinhangka'] && $data['name'] && $data['tel'] && $data['zcard'] && $data['fcard'] && $data['card'] && $data['yinhangka']) {
            // echo '111';

            $kaihu = M('cskaihu');
            //var_dump($data);
            $hangshu = $kaihu->add($data);


            if ($hangshu) {
                $this->redirect('Home/User/tishi');


            } else {
                $this->error('<h1>失败,请检查填写内容</h1>');
            }

        } else {
            $this->error('<h1>您有未填写的选项,请填写完毕,谢谢!</h1>');
        }


    }

    public function doshenqing()
    {
        /*$user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/index/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }*/

        //$data['name'] = isset($_POST['name'])?trim($_POST['name']):'';
        //$data['id'] = $chaxunarr['id'];
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        // $data['cart'] = isset($_POST['cart'])?trim($_POST['cart']):'';
        //$data['weixinhao'] = isset($_POST['weixinhao'])?trim($_POST['weixinhao']):'';
        $data['tel'] = isset($_POST['tel']) ? trim($_POST['tel']) : '';
        $data['openid'] = isset($_POST['openid']) ? trim($_POST['openid']) : '';
        // $data['diqu'] = isset($_POST['diqu'])?$_POST['diqu']:'';
        //$data['xiangxi'] = isset($_POST['xiangxi'])?$_POST['xiangxi']:'';
        if ($data['name'] && $data['tel']) {
            // echo '111';
            $user = M('user');
            $hangshu = $user->where(array('openid'=>$data['openid']))->save($data);



            if ($hangshu) {
                $user->where(array('openid'=>$data['openid']))->save(array('zhuangtai'=>1));
                $this->redirect('Home/User/tishi');


            } else {
                $this->redirect('Home/User/error2/type/2');
            }

        } else {
            $this->redirect('Home/User/error2/type/1');
        }


    }

    public function doedit()
    {
        header("Content-type:text/html;charset=utf-8");
        $data['openid'] = $_SESSION['userinfo']['openid'];
        $data['id'] = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $data['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
        $data['cart'] = isset($_POST['cart']) ? trim($_POST['cart']) : '';
        $data['weixinhao'] = isset($_POST['weixinhao']) ? trim($_POST['weixinhao']) : '';
        $data['headimgurl'] = isset($_POST['headimgurl']) ? trim($_POST['headimgurl']) : '';
        $data['tel'] = isset($_POST['tel']) ? trim($_POST['tel']) : '';
        $data['diqu'] = isset($_POST['diqu']) ? $_POST['diqu'] : '';
        $data['xiangxi'] = isset($_POST['xiangxi']) ? $_POST['xiangxi'] : '';
        if ($data['name'] && $data['tel']) {
            // echo '111';
            $user = M('user');
            $user->where(array('openid' => $data['openid']))->save($data);
            $openidarr = $user->where(array('id' => $data['id']))->field('openid,dizhi')->find();
            $dizhiid = $openidarr['dizhi'];
            $dizhi = M('dizhi');
            $dizhiarr['id'] = $dizhiid;
            $dizhiarr['shoujianren'] = $data['name'];
            $dizhiarr['dianhua'] = $data['tel'];
            $dizhiarr['diqu'] = $data['diqu'];
            $dizhiarr['xiangxi'] = $data['xiangxi'];
            //$dizhiarr['youbian']=$_POST['youbian'];
            $hangshu2 = $dizhi->where(array('openid' => $data['openid']))->save($dizhiarr);
            if ($hangshu2 !== false) {
                $this->redirect('Home/User/tishi');
            } else {
                $this->redirect('Home/User/tishix');
            }


        } else {
            $this->error('<h1>您有未填写的选项,请填写完毕,谢谢!</h1>');
        }


    }

    public function doyinhangka()
    {
        $user = M('user');
        $name = $_POST['yinhangname'];
        $yinhangka = $_POST['yinhangka'];
        $openid = $_POST['openid'];
        $hangshu = $user->where(array('openid' => $openid))->save(array('yinhangname' => $name, 'yinhangka' => $yinhangka));

        $this->redirect('Home/User/tishi');

    }

    public function tishi()
    {
        $this->display();
    }

    public function yinhangkaxinxi()
    {

        $openid = $_GET['openid'];

        $user = M('user');
        $result = $user->where(array('openid' => $openid))->field('nickname,yinhangname,yinhangka,jibie')->find();
        // $_SESSION['userinfo']['name']=$result[]
        if ($result['jibie'] == 0) {
            $jibie = '您还不是代理';
        } elseif ($_SESSION['userinfo']['jibie'] == 1) {
            $jibie = '特约代理';
        } elseif ($_SESSION['userinfo']['jibie'] == 2) {
            $jibie = '三级代理';
        } elseif ($_SESSION['userinfo']['jibie'] == 3) {
            $jibie = '二级代理';
        } elseif ($_SESSION['userinfo']['jibie'] == 4) {
            $jibie = '四级代理';
        } elseif ($_SESSION['userinfo']['jibie'] == 5) {
            $jibie = '总代理';
        } elseif ($_SESSION['userinfo']['jibie'] == 6) {
            $jibie = '核心代理';
        }


        $this->assign('jibie', $jibie);

        $this->assign('result', $result);

        $this->display();


    }

    public function yinhangka()
    {
        $openid = $_GET['openid'];

        $user = M('user');
        $result = $user->where(array('openid' => $openid))->field('yinhangname,yinhangka')->find();
        if (empty($result['yinhangka']) || empty($result['yinhangname'])) {
            $this->assign('result', $result);

            $this->display();
        } else {

            $this->redirect('Home/User/yinhangkaxinxi/openid/' . $openid . '');

        }
        // $_SESSION['userinfo']['name']=$result[]


    }

    public function xiugaiyinhangka()
    {
        $openid = $_GET['openid'];

        $user = M('user');
        $result = $user->where(array('openid' => $openid))->field('yinhangname,yinhangka')->find();

        if ($result['jibie'] == 0) {
            $jibie = '您还不是代理';
        } else {
            $jibie = $this->daili($result['jibie']);
        }

        $this->assign('jibie', $jibie);
        $this->assign('result', $result);

        $this->display();


    }

    public function shouquan()
    {
        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/shouquan/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }


        $this->display();


    }
    /*
	 * 申请成为居间商*/
    public function juJianshang(){
        $openid=$_GET['openid'];
        $hangshu=M('shenqingliebiao')->where(array('openid'=>$openid))->find();
        if($hangshu['status']==1){
            $this->error('您的申请正在审核中，请勿重复申请！');
        }elseif($hangshu['status']==2){
            $this->error('您已是居间商，请勿重复申请！');
        }else{
            $userarr=M('user')->where(array('openid'=>$openid))->find();
            $this->assign('list',$userarr);
            $this->display();
        }

    }
      /*
       * 居间商申请处理*/
    public function update(){
        $data=I('post.');
        
    }

    public function my_frm()
    {

        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/my_frm/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        if ($_SESSION['userinfo']['jibie'] == 0) {
            $jibie = '您还不是代理';
        } else {
            $jibie = $this->daili($_SESSION['userinfo']['jibie']);
        }
        //echo $reurl;
        //var_dump($a);


        if (empty($_SESSION['userinfo']['cart'])) {
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }
        $this->assign('jibie', $jibie);
        $this->display();


    }

    /*
     *
     * 获取到用户交易账号
     * */
    public function jiaoyi_zhanghao()
    {
        //header("Content-type:text/html;charset=utf-8");
        $openid = $_GET['openid'];//获取到用户的身份信息 匹配交易数据获取到用户的开户账号
        $user = M('user');
        $zhanghao = $user->where(array('openid' => $openid))->find();

        $this->assign('zhanghao', $zhanghao);
        $this->display();

    }

    /*结算佣金(辽宁联合）*/
    public function savejb()
    {
        $openid = $_GET['openid'];
        $user = M('user');
        $zhanghao = $user->where(array('openid' => $openid))->field('ln_dengluzhanghao')->find();
        $jiaoyi = M('jiaoyishuju');
        $userjiaoyi = $jiaoyi->where(array('zhanghao' => $zhanghao))->find();
        $shouxu = $userjiaoyi['jysxf'];

    }

    public function renmai()
    {


        $sid = $_GET['id'];

        $where['sid'] = $sid;
        $user = M('user');
        $myxj = $user->where($where)->select();
        if ($myxj != "") {
            foreach ($myxj as $k => $value) {
                $myxj[$k]['jibie'] = $this->daili($value['jibie']);
            }
        }
        $this->assign('myxj', $myxj);
        //var_dump($myxj);
        $this->display();


    }


    public function user_index()
    {

        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/user_index/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        if ($_SESSION['userinfo']['jibie'] == 0) {
            $jibie = '您还不是代理';
        } else {
            $jibie = $this->daili($_SESSION['userinfo']['jibie']);
        }
        //echo $reurl;
        //var_dump($a);

        $huiyuanshu = M('user')->where(array('sid' => $_SESSION['userinfo']['id']))->count();

        if (empty($_SESSION['userinfo']['tel'])) {
            $this->redirect('Home/User/shenqingdaili/id/' . $_SESSION['userinfo']['id']);
        }
        $jiner=M('yongjinjilu')->where(array('userid'=>$_SESSION['userinfo']['id']))->sum('jiner');
        $keti=$jiner*0.935;
        $jifen=$jiner*0.8;
        $this->assign('keti',$keti);
        $this->assign('jinbi',$jiner);
        $this->assign('jifen',$jifen);
        $this->assign('huiyuanshu', $huiyuanshu);
        $this->assign('jibie', $jibie);
        $this->display();


    }

    public function xuser_index()
    {

    }

    public function daili($i)
    {
        if ($i == 0) {
            $dailiming = '注册用户';
        } elseif ($i == 1) {
            $dailiming = '正式会员';
        } elseif ($i == 2) {
            $dailiming = '普卡';
        } elseif ($i == 3) {
            $dailiming = '银卡';
        } elseif ($i == 4) {
            $dailiming = '金卡';
        } elseif ($i == 5) {
            $dailiming = '白金卡';
        } elseif ($i == 6) {
            $dailiming = '白金卡';
        }
        return $dailiming;
    }

    public function sqtx()
    {


        $tixian = D('tixian');
        $user = D('user');
        $openid = $_GET['openid'];
        $userarr = $user->where(array('openid' => $openid))->find();
        if ($userarr['jinbi'] >= 100) {
            $id = $userarr['id'];
            /* if(strlen($_SESSION['userinfo']['tel'])<5){
                 $this->redirect('Wx/tishix');
                 exit;
             }*/


            $barr = $tixian->where(array('openid' => $openid))->max('id');//找到用户最近一次提现记录的id值

            if (!isset($barr)) {
                //$user = D('user');
                //$jinbiarr = $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->field('jinbi')->find();
                //$jinbiarr['jinbi']=$_GET['yongjin'];

                //找到可提现佣金

                $ketixiannew = new \Home\Controller\DingdanController();
                $ketixian = $ketixiannew->ketixian($id);
                //   $ketixianarr = $ketixiannew->ketixianarr($id);


                //


                $this->assign('ketixian', $ketixian);
                $this->display();

            } else {
                $carr = $tixian->where(array('id' => (int)$barr))->find();//找到用户最近一次提现的所有记录
                $status = $carr['status'];//用户的提款状态 0代表正在提款中,1代表已经提款完毕

                if ($status == 0) {

                    $this->display('dengdai');
                    exit;
                } else {

                    //判断用户有多少金币
                    // $user = D('user');
                    // $jinbiarr = $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
                    // $jinbiarr['jinbi']=$_GET['yongjin'];

                    //  $this->assign('jinbiarr',$jinbiarr);
                    // $this->assign('dingdanhao',$_GET['dingdanhao']);
                    //$this->display();
                    //找到可提现佣金

                    $ketixiannew = new \Home\Controller\DingdanController();
                    $ketixian = $ketixiannew->ketixian($id);


                    $this->assign('ketixian', $ketixian);
                    $this->display();

                }
            }

        } else {
            $this->display('tishi2');
        }
    }


    /*
     * 提现操作入库*/
    public function txrk()
    {
        $openid = $_GET['openid'];
        $user = M('user');
        $userarr = $user->where(array('openid' => $openid))->field('nickname,headimgurl,openid,name,tel')->find();
        $arr = $user->where(array('openid' => $openid))->find();
        $jinbi = $arr['jinbi'];
        /* $userarr=$user->where(array('openid'=>$openid))->find();
         var_dump($userarr);
         exit;*/
        $data['nickname'] = $userarr['nickname'];
        $data['username'] = $userarr['name'];
        $data['tel'] = $userarr['tel'];
        $data['headimgurl'] = $userarr['headimgurl'];
        $data['openid'] = $userarr['openid'];
        $data['addtime'] = time();
        $data['money'] = isset($_POST['money']) ? trim($_POST['money']) : '';
        $data['yinhangka'] = isset($_POST['yinhangka']) ? trim($_POST['yinhangka']) : '';
        $data['zhanghao'] = isset($_POST['zhanghao']) ? trim($_POST['zhanghao']) : '';
        if ($data['money'] && $data['yinhangka'] && $data['zhanghao']) {
            $tixian = M('tixian');
            $ruku = $tixian->add($data);
            if ($ruku) {
                $this->redirect('Home/User/tishi');
                return;
            } elseif ($data['money'] > $jinbi) {
                $this->error('<h1>失败,您输入内容有误</h1>');
            }
            return;
        } else {
            $this->error('<h1>信息填写不完整,请填写完毕,谢谢!</h1>');
        }
    }


    /*帮助
     * 使用教程*/
    public function xinshou(){
       /* $month = date('m');
        $year = date('Y');
        $last_month = date('m') - 1;

        if($month == 1){
            $last_month = 12;
            $year = $year - 1;
        }
        $beginTime=mktime(0, 0, 0, $last_month, 1, $year);
        $endTime=mktime(0, 0, 0, $month, 1, $year);
        var_dump($beginTime);
        var_dump($endTime);*/



        //var_dump($BeginDate);
        //$dayBegin = strtotime(date('Y-m-d', time()));
        //$date3=date('Y-m-d H:i:s',$dayBegin);
        //var_dump($date3);

       // var_dump($dayBegin);

// 当天的24
        //$dayEnd = $dayBegin + 24 * 60 * 60;
        //var_dump($dayEnd);


    }
    public function jiaocheng()
    {
        //$wen = M('article');
        $wen=M('category');
        $jiao = $wen->where("pid=7")->select();
        $this->assign('jiao', $jiao);
        $this->display();
        
        /*foreach ($jiao as $k=>$v){
            $id=$v['id'];
            $wen1 = M('category');
            $jiao1 = $wen1->where(array('sid'=>$id))->select();
            //var_dump($jiao);
            //exit;
            $this->assign('jiao1',$jiao1);
            $this->display();

        }*/








    }
    public function jclist()
    {
        $wen = M('article');
        $where['sid'] = I("id");
        $jiao = $wen->where($where)->select();
        $this->assign('jiao', $jiao);
        $this->display();
        /* foreach ($jiao as $k=>$v){
             $id=$v['id'];
             $wen1 = M('category');
             $jiao1 = $wen1->where(array('sid'=>$id))->select();
             //var_dump($jiao);
             //exit;
             $this->assign('jiao1',$jiao1);
             $this->display();
     }*/
    }


    public function jcxq()
    {
        $wen = M('article');
        $where['aid'] = I("aid");
        $jiao = $wen->where($where)->find();
        $this->assign('jiao', $jiao);
        $this->display();
    }

    public function tongzhi()
    {
        $wen = M('article');
        $tongzhi = $wen->where("sid=8")->select();
        $this->assign('tongzhi', $tongzhi);
        $this->display();
    }

    public function tongzhixq()
    {
        $wen = M('article');
        $where['aid'] = I("aid");
        $tongzhi = $wen->where($where)->find();
        $this->assign('tongzhi', $tongzhi);
        $this->display();

    }

    public function chuangye()
    {
        $wen = M('article');
        $chuangye = $wen->where("sid=9")->select();
        $this->assign('chuangye', $chuangye);
        $this->display();
    }

    public function chuangyexq()
    {
        $wen = M('article');
        $where['aid'] = I("aid");
        $chuangye = $wen->where($where)->find();
        $this->assign('chuangye', $chuangye);
        $this->display();
    }

    public function xiazai()
    {
        $wen = M('article');
        $ruanjian = $wen->where("sid=14")->select();
        $this->assign('ruanjian', $ruanjian);
        $this->display();
    }

    public function xxq()
    {
        $wen = M('article');
        $where['aid'] = I("aid");
        $xq = $wen->where($where)->find();
        $this->assign('xq', $xq);
        $this->display();
    }

    public function zbs()
    {
        $wen = M('article');
        $zbs = $wen->where("sid=15")->select();
        $this->assign('zbs', $zbs);
        $this->display();
    }
    public function zbyy(){
        $wen = M('article');
        $where['aid'] = I("aid");
        $yy = $wen->where($where)->find();
        $this->assign('yy', $yy);
        $this->display();
    }

    /*
     * 二维码*/
    public function ewm(){
        $wen = M('article');
        $zbs = $wen->where("sid=20")->select();
        $this->assign('erweima', $zbs);
        $this->display();
    }

    public function my_info()
    {
        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/my_info/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $jibie = $this->daili($_SESSION['userinfo']['jibie']);

        $dizhi = M('dizhi');
        $maxid = $dizhi->where(array('openid' => $_SESSION['userinfo']['openid']))->max('id');
        $dizhiarr = $dizhi->where(array('id' => $maxid))->find();
        $this->assign('list', $dizhiarr);


        $this->assign('jibie', $jibie);
        $this->display();

    }
    /*判断用户关系*/
    /*public function guanxi(){
        $user = D('user');
        if(empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/guanxi/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }else{

            $chaxunarr =  $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $result=$user->where(array('sid'=>$_SESSION['userinfo']['id']))->count();

    }*/

    public function ceshi()
    {
        // echo dirname(__FILE__).'<br>';
        //echo __PUBLIC__.'<br>';
        //echo substr(dirname(__FILE__),0,14);
        $user = D('user');
        echo $user->max('id');
    }

    public function cuowu($sjopenid, $zuozhe)
    {

        //参数是二维码里面带的上级用户的openid
        $options = array(
            'token' => 'zzruisi', //填写你设定的key
            'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
            'appid' => C(APPID), //填写高级调用功能的app id
            'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj = new Util\Wechatnew($options);
        // $weObj->setTMIndustry(1);
        $user = D('user');
        $zuozhearr = $user->where(array('openid' => $zuozhe))->find();
        $f = '您好,微信昵称为[' . $zuozhearr['nickname'] . ']的朋友,在' . date("Y-m-d H:i:s", $zuozhearr['addtime']) . '已经成为系统的会员,会员号为' . $zuozhearr['huiyuanhao'] . ',因此不能重复关注,感谢您的支持';


        $data = array("touser" => $sjopenid,
            "template_id" => 'FjHssGIOIQP39C0P620PQrsGvAe7C-L7gNbF68U1Uaw',
            "url" => C(URL) . '/index.php/Home/Wx/user',
            "topcolor" => "#7B68EE",
            'data' => array('first' => array('value' => $f,
                'color' => "#743A3A",
            ),
                'keyword1' => array('value' => '重复关注',
                    'color' => "#743A3A",
                ),
                'keyword2' => array('value' => '已经有上级的不能重复关注',
                    'color' => "#743A3A",
                ),
                'remark' => array('value' => "感谢您的支持",
                    'color' => "#743A3A",
                )


            )


        );


        $weObj->sendTemplateMessage($data);


    }

    public function se($canshu, $zuozhe, $hyh)
    {

        /*  ｛
        "touser":"OPENID",
			"template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
			"url":"http://weixin.qq.com/download",
			"topcolor":"#FF0000",
			"data":{
            "参数名1": {
                "value":"参数",
					"color":"#173177"	 //参数颜色
					},
				"Date":{
                "value":"06月07日 19时24分",
					"color":"#173177"
					},
				"CardNumber":{
                "value":"0426",
					"color":"#173177"
					},
				"Type":{
                "value":"消费",
					"color":"#173177"
					}
			}
		}
        */

        //参数是二维码里面带的上级用户的openid
        $options = array(
            'token' => 'zzruisi', //填写你设定的key
            'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
            'appid' => C(APPID), //填写高级调用功能的app id
            'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj = new Util\Wechatnew($options);
        // $weObj->setTMIndustry(1);
        $sjarr = $weObj->getUserInfo($canshu);
        $zuozhearr = $weObj->getUserInfo($zuozhe);

        $data = array("touser" => $canshu,
            "template_id" => 'mc7kDMEnlCpLdMTor4F3aDAb2wBjbjLe8nvQnY-JMJ4',
            "url" => C(URL) . '/index.php/Home/Wx/user',
            "topcolor" => "#7B68EE",
            'data' => array('first' => array('value' => '您好,' . $sjarr['nickname'] . ',' . $zuozhearr['nickname'] . '会员号为[' . $hyh . ']刚刚通过二维码关注我们',
                'color' => "#743A3A",
            ),
                'keyword1' => array('value' => $zuozhearr['nickname'],
                    'color' => "#743A3A",
                ),
                'keyword2' => array('value' => date("Y-m-d H:i:s", time()),
                    'color' => "#743A3A",
                ),
                'keyword3' => array('value' => $sjarr['nickname'],
                    'color' => "#743A3A",
                ),
                'remark' => array('value' => "您的推广卓有成效,请继续保持",
                    'color' => "#743A3A",
                )


            )


        );


        //找到上级id
        $user = D('user');
        $sidarr = $user->where(array('openid' => $zuozhe))->find();
        $sid = $sidarr['sid'];
        $i = 0;

        while ($sid > 1 && $i < 3) {

            $sidarra = $user->where(array('id' => $sid))->find();//找到上一级id

            $data = array("touser" => $sidarra['openid'],
                "template_id" => 'xtsMrZTi4L7oRg9WTDv50oCpcF4OKNE-bJ27VNPbcSY',
                "url" => C(URL) . '/index.php/Home/Wx/user',
                "topcolor" => "#7B68EE",
                'data' => array('first' => array('value' => '您好,' . $sidarra['nickname'] . ',' . $zuozhearr['nickname'] . '会员号为[' . $hyh . ']刚刚通过二维码关注我们',
                    'color' => "#743A3A",
                ),
                    'keyword1' => array('value' => $zuozhearr['nickname'],
                        'color' => "#743A3A",
                    ),
                    'keyword2' => array('value' => date("Y-m-d H:i:s", time()),
                        'color' => "#743A3A",
                    ),
                    'keyword3' => array('value' => $sjarr['nickname'],
                        'color' => "#743A3A",
                    ),
                    'remark' => array('value' => "您的推广卓有成效,请继续保持",
                        'color' => "#743A3A",
                    )

                )


            );
            $weObj->sendTemplateMessage($data);

            $sid = $sidarra['sid'];
            $i++;
        }


    }

    /*
     *
     * @________用户入库
     * $param $zuozhe 传入用户的openid
     *
     *
     *
     * */
    public function saveuser($zuozhe)
    {

        $options = array(
            'token' => 'zzruisi', //填写你设定的key
            'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
            'appid' => C(APPID), //填写高级调用功能的app id
            'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
        );
        $user = D('user');
        $weObj = new Util\Wechatnew($options);

        $mhuiyuanhao = $user->max('huiyuanhao');
        $ida = $user->data(array('huiyuanhao' => $mhuiyuanhao + 17))->add();


        //判断之前是否存过

        //判断之前是否存储过

        $userdata = $weObj->getUserInfo($zuozhe);//得到用户的信息   是个数组
        $userdata['addtime'] = time();
        $userdata['id'] = $ida;
        // $userdata['huiyuanhao']=$mhuiyuanhao+17;
        //  $userdata['huiyuanhao']=$this->huiyuanhao();
        $user->save($userdata);
        $res = $user->where(array('id' => $ida))->find();
        return $res['huiyuanhao'];
        //  $weObj->text('您的昵称是'.$userdata['nickname'].'您于'.date("Y年m月d日 H时i分s秒",$userdata['addtime']).',成为本系统的第'.$userdata['huiyuanhao'].'位用户')->reply();


    }

    public function huiyuanhao()
    {
        $user = D('user');

    }

    //生成二维码操作
    public function shengcheng()
    {

        $user = D('user');
        /*组合二维码里面的值
         * 值+openid
         *
         * */

        $openid = $_GET['openid'];
        $canshu = $openid;


        $jichu = new JichuController();

        $imgurl = $jichu->erweima($canshu);


        $this->ruku($imgurl, $openid);

        //  $xiaoxi= new \Home\Controller\XiaoxiController();
        // $xiaoxi->addxiaoxi($_SESSION['userinfo']['openid'],'生成二维码');

        $this->redirect('Home/Anquan/erweima/openid/' . $openid);


    }

    /**
     * 生成唯字符串
     * @return string
     */
    function theonly()
    {

        return date('YmdHis', time()) . substr(microtime(), 2, 6);
    }

    /*
     * 生成授权图片
    * $name 姓名，$phone 手机号，$daili 代理级别，$qixian 代理期限 ，$erweima 授权二维码，$sqbm 授权编码
    */
    public function wodeshouquan()
    {
        $image = new \Think\Image();
        $where['openid'] = $_GET['openid'];
        if (empty($_GET['openid'])) {
            $this->error('参数错误！');
            exit;
        }
        $user = D('user');
        $userinfo = $user->where($where)->find();

        if ($userinfo['shouquantu'] == "") {
            $erweima = $this->shouquanerweima($_GET['openid']);
            $daili = $this->daili($userinfo['jibie']);
            //echo $erweima;exit;
            $newname = $this->theonly() . '.png';

            //缩小二维码
            $image->open('.' . $erweima);
            $image->thumb(130, 130, \Think\Image::IMAGE_THUMB_SCALE)->save('./Uploads/shouquan/twm.png');
            //加水印
            $qixian = date('Y-m-d') . '至' . date('Y-m-d', strtotime('+1 year', time()));
            $image->open('./Uploads/shouquan/sq.png')->text($userinfo['name'], './Uploads/shouquan/wrkt.ttf', 16, '#000000', array('275.28', '500.64'))
                ->text($userinfo['tel'], './Uploads/shouquan/wrkt.ttf', 16, '#000000', array('480.44', '500.64'))
                ->text($daili, './Uploads/shouquan/wrkt.ttf', 16, '#000000', array('335', '590'))
                ->text($qixian, './Uploads/shouquan/wrkt.ttf', 16, '#000000', array('310', '675'))
                ->text($userinfo['id'], './Uploads/shouquan/wrkt.ttf', 16, '#000000', array('200', '980'))
                ->water('./Uploads/shouquan/twm.png', array('337.00', '799.00'))
                ->save('./Uploads/shouquan/' . $newname);
            unlink('./Uploads/shouquan/twm.png');
            $data['shouquantu'] = '/Uploads/shouquan/' . $newname;
            $statut = $user->where($where)->save($data);
            if ($statut === 1) {
                $this->assign('sqt', '/Uploads/shouquan/' . $newname);
                $this->display();
            } else {
                $this->error('授权图录入失败！');
            }
        } else {
            $this->assign('sqt', $userinfo['shouquantu']);
            $this->display();
        }


    }

    public function shouquanerweima($openid)
    {
        $zhi = 10;//获取用户生成的是几级二维码

        $user = D('user');/*组合二维码里面的值
         * 值+openid
         *
         * */;
        $canshu = $zhi . '=' . $openid;
        $shouquanarr = $user->where(array('openid' => $openid))->field('shouquanerweima')->find();

        if (empty($shouquanarr['shouquanerweima'])) {
            $jichu = new JichuController();

            $imgurl = $jichu->erweima($canshu);


            $this->rukushouquan($imgurl, $openid);
            return substr($imgurl, 1);
        } else {

            return $shouquanarr['shouquanerweima'];
        }


        //  $xiaoxi= new \Home\Controller\XiaoxiController();
        // $xiaoxi->addxiaoxi($_SESSION['userinfo']['openid'],'生成二维码');


    }

    public function shenhe()
    {
        $sid = $_GET['sid'];

        $user = D('user');

        $shenhe = D('shenhe');
        $allshenhe = $allshenhe = $shenhe->where(array('sid' => $sid))->order('zhuangtai DESC')->select();//所有审核的东西

        $this->assign('list', $allshenhe);

        $this->display();


    }

    public function doshenhe()
    {
        $userid = $_GET['userid'];
        $shenhe = M('shenhe');
        $user = M('user');
        $shenhehangshu = $shenhe->where(array('userid' => $userid, 'zhuangtai' => 0))->save(array('zhuangtai' => 1));//扎到未审核且即将要进行审核的用户
        $userhangshu = $user->where(array('id' => $userid))->save(array('shenhe' => 1));
        if ($shenhehangshu && $userhangshu) {
            $this->success('审核成功');
        }


    }

    public function myerweima()
    {
        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/User/myerweima/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $jibie = $_SESSION['userinfo']['jibie'];
        if ($_SESSION['userinfo']['shenhe'] == 0 || $_SESSION['userinfo']['shenhe'] == 2) {
            $this->redirect('tishix');
        }
        //echo $jibie;

        if ($jibie > 1) {

            for ($i = 1; $i < $jibie; $i++) {
                $opt[$i] = $this->daili($i);
            }

        } else {
            $this->error('您的级别无法生成二维码');

        }

        $this->assign('opt', $opt);

        $this->display();


    }

    public function denglu()
    {
        //var_dump($_POST);
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user = D('user');

        /* $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        $reurl = C(URL).U('Home/Wx/denglu/');
        if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);*/

        $idar = $user->where(array('openid' => $_POST['openid'], 'username' => $username, 'password' => $password))->find();
        if ($idar['id']) {
            //$_SESSION['userinfo']=$idar;
            if ($idar['zhuangtai'] == 1) {
                $this->redirect('Wx/user');
            } else {
                //T('Admin@Public/menu')
                $this->display(T('Home@Wx/tishi'));
                exit;

            }
        } else {
            $this->error('登录失败');

        }
    }

    public function shangji($zuozhe, $canshu)
    {
        $options = array(
            'token' => 'zzruisi', //填写你设定的key
            'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
            'appid' => C(APPID), //填写高级调用功能的app id
            'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
        );
        $user = D('user');
        $weObj = new Util\Wechatnew($options);
        //通过$canshu找到上级ID号
        $shangjiarr = $user->where(array('openid' => $canshu))->find();
        //找到个人信息id号
        $woarr = $user->where(array('openid' => $zuozhe))->find();
        //存入数据库
        if ($woarr['sid'] == 1 && !($zuozhe == $canshu) && $woarr['id'] > $shangjiarr['id']) {
            $user->where(array('id' => $woarr['id']))->save(array('sid' => $shangjiarr['id'], 'shangjiname' => $shangjiarr['nickname']));

        }

    }

    public function ruku($imgurl, $openid)
    {
        $user = D('user');
        $idarr = $user->where(array('openid' => $openid))->find();
        //$user->data(array('erweima' => substr($imgurl,1),'openid'=>$openid))->add();
        $user->where(array('id' => $idarr['id']))->save(array('erweima' => substr($imgurl, 1)));


    }

    public function rukushouquan($imgurl, $openid)
    {
        $user = D('user');
        $idarr = $user->where(array('openid' => $openid))->find();
        //$user->data(array('erweima' => substr($imgurl,1),'openid'=>$openid))->add();
        $user->where(array('id' => $idarr['id']))->save(array('shouquanerweima' => substr($imgurl, 1)));
    }

    public function panduanchongfu($zuozhe)
    {
        $user = D('user');
        $chaxundata = $user->where(array('openid' => $zuozhe))->find();
        if (!empty($chaxundata)) {
            return $chaxundata;
        } else {

            return false;
        }

    }

    public function zhuce()
    {
        $options = array(
            'token' => 'zzruisi', //填写你设定的key
            'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
            'appid' => C(APPID), //填写高级调用功能的app id
            'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj = new Util\Wechatnew($options);
        $reurl = C(URL) . U('Home/Wx/zhuce/');

        if (empty($_GET['code'])) {
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        } else {
            //$code = $_GET['code'];
            $a = $weObj->getOauthAccessToken();
        }
        // var_dump($a);
        //$weixinurl =  $weObj->getOauthRedirect($reurl);//获取到的是微信授权页面的url 需要去访问一下
        $userinfo = $weObj->getOauthUserinfo($a['access_token'], $a['openid']);
        $_SESSION['userinfo'] = $userinfo;
        $user = D('user');
        $zarr = $user->where(array('openid' => $userinfo['openid']))->find();


        /* if ($zarr['zhuangtai'] == 0) {

                $this->assign('userinfo', $userinfo);

                $this->display();
            } elseif ($zarr['zhuangtai'] == 1) {}*/
        $_SESSION['userinfo'] = $zarr;
        $xiaoxi = new \Home\Controller\XiaoxiController();
        $xiaoxi->addxiaoxi($zarr['openid'], '登录');
        $this->redirect('Wx/user');


        //第一步提供一个跳转链接  给授权链接 然后返回一个code参数芳到跳转链接的后面  例如:redirect_uri/?code=CODE&state=STATE

        //第二步通过跳转了解换取token //这个token和基础支持的token不一样
        //第三步 刷新token (如果需要的话)
        //第四部拉取用户信息

    }

    public function tijiao()
    {
        $arr = $_POST;
        $t = array_keys(array_map('trim', $arr), '');
        if ($t) {
            $this->error('填写有误,请检查');

        } else {
            // echo '1111111';
            $zarr = $arr['openid'];
            $user = D('user');
            $chaxunarr = $user->where(array('openid' => $zarr))->find();
            $fid = $user->where(array('id' => $chaxunarr['id']))->save($arr);


            if ($fid) {
                if ($fid['zhuangtai'] == 1) {
                    $_SESSION['userinfo'] = $chaxunarr;
                    $this->redirect('Wx/user');

                } else {

                    $this->display(T('Home@Wx/tishi'));
                    exit;

                }
            }

        }
        exit;
    }
    /*  public function login(){
        $options = array(
            'token'=>'zzruisi', //填写你设定的key
            'encodingaeskey'=>C(ENCO), //填写加密用的EncodingAESKey
            'appid'=>C(APPID), //填写高级调用功能的app id
            'appsecret'=>C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj= new Util\Wechatnew($options);
        $reurl = C(URL).U('Home/Wx/login/');

        if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        // var_dump($a);
        //$weixinurl =  $weObj->getOauthRedirect($reurl);//获取到的是微信授权页面的url 需要去访问一下
        $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);
        $this->assign('userinfo',$userinfo);

        //$this->display();

        $this->display();

    }*/
    /**
     *
     */
    public function user()
    {
        // $this->tijiao($_POST);


        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/user/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        } else {

            $chaxunarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $tixian = D('tixian');
        $dingdan = D('dingdan');
        $yijing = 0;//总计提现金额
        $dingdanshu = $dingdan->where(array('openid' => $chaxunarr['openid']))->count();
        $jinbiarr = $tixian->where(array('openid' => $_SESSION['userinfo']['openid']))->select();
        $xiaoxi = D('xiaoxi');
        $xiaoxicount = $xiaoxi->where(array('openid' => $_SESSION['userinfo']['openid'], 'status' => 0))->count();

        foreach ($jinbiarr as $key => $jinbia) {
            $yijing += $jinbia['jiner'];

        }
        $mmm = $yijing + $chaxunarr['jinbi'];
        //已经付款的下级个数
        //得到所有下级arr

        $ct = count($jinbiarr);
        $xiajiidarr = $this->xiajiidarr($_SESSION['userinfo']['id']);//所有下级的列表
        if (is_array($xiajiidarr)) {
            /* $pp = array_unique($xiajiidarr);
            //$tgd =0;
            $tgdd = 0;
            foreach($pp as $xiajik){
                $tgd =$dingdan->where(array('sjid'=>array('in',$)))->select();

                $tgdd += count($tgd);
            }*/
            $tgdd = $dingdan->where(array('sjid' => array('in', $xiajiidarr)))->count();//所有订单数
        } else {
            $tgdd = 0;
        }
        $yizhifuusercount = 0;
        $weizhifuusercount = 0;
        if (is_array($xiajiidarr)) {
            $xiajicount = count($xiajiidarr);
            $where['id'] = array('IN', $xiajiidarr);
            $where['zhuangtai'] = 1;
            $yizhifuusercount = $user->where($where)->count();

            $wher['id'] = array('IN', $xiajiidarr);
            $wher['zhuangtai'] = 0;
            $weizhifuusercount = $user->where($wher)->count();

        } else {
            $xiajicount = 0;
        }


        $dizhi = D('dizhi');
        $dizhishu = $dizhi->where(array('openid' => $_SESSION['userinfo']['openid']))->count();
        $_SESSION['userinfo']['mmm'] = $mmm;

        // $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
        //$fid = $user->where(array('id' => $chaxunarr['id']))->save($arr);

        // $userinfo = $weObj->getOauthUserinfo($a['access_token'],$a['openid']);
        //$_SESSION['userinfo'] = $chaxunarr;
        $this->assign('huiyuancount', $weizhifuusercount);
        // $this->assign('zfcount',$yizhifuusercount);
        $this->assign('dizhishu', $dizhishu);
        $this->assign('xjzfcount', $yizhifuusercount);
        $this->assign('ct', $ct);
        $this->assign('tgdd', $tgdd);
        $this->assign('mmm', $mmm);
        $this->assign('xjcount', $xiajicount);
        $this->assign('dingdanshu', $dingdanshu);
        $this->assign('yijing', $yijing);
        $this->assign('xiaoxicount', $xiaoxicount);

        $this->display();

    }

    public function xiajigeshu()
    {
        $user = D('user');

        $id = $_SESSION['userinfo']['id'];
        $yiji = $user->where(array('sid' => $id))->count();
        if (!$yiji) {
            return 0;
            exit;
        };

        $yarr = $user->where(array('sid' => $id))->select();

        foreach ($yarr as $aaa) {

            $erjiarr[] = $aaa['id'];

        }

        $erjirenshu = $user->where(array('sid' => array('IN', $erjiarr)))->count();//本质是二级的个数
        if (!$erjirenshu) {
            return $yiji;

        }
        $sanjiarrbbb = $user->where(array('sid' => array('IN', $erjiarr)))->select();//
        foreach ($sanjiarrbbb as $sjarr) {
            $sanjiarr[] = $sjarr['id'];
        }

        $sj = $user->where(array('sid' => array('IN', $sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数
        if (!$sj) {
            return $yiji + $erjirenshu;
            exit;
        }
        return $sj + $erjirenshu + $yiji;


    }


    public function xiajiidarr($id)
    {
        $user = D('user');


        /*if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
        $_SESSION['userinfo'] = $chaxunarr;*/
        $yiji = $user->where(array('sid' => $id))->count();
        if ($yiji) {
            $yarr = $user->where(array('sid' => $id))->select();
            foreach ($yarr as $aaa) {

                $erjiarr[] = $aaa['id'];
                // $user->where(array('sid'=>$value['id']))->select();


            }


            $erjirenshu = $user->where(array('sid' => array('IN', $erjiarr)))->count();//本质是二级的个数
            if ($erjirenshu) {


                $sanjiarrbbb = $user->where(array('sid' => array('IN', $erjiarr)))->select();//
                foreach ($sanjiarrbbb as $sjarr) {
                    $sanjiarr[] = $sjarr['id'];
                }


                $sj = $user->where(array('sid' => array('IN', $sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数
                $sijiarrccc = $user->where(array('sid' => array('IN', $sanjiarr)))->select();

                if ($sj) {
                    foreach ($sijiarrccc as $sjshuzu) {
                        $sijiarr[] = $sjshuzu['id'];

                    }


                }


            } else {


            }


        } else {


        }


        /*$erjicount=0;

        foreach($yarr as $value){ //循环出每个人的下级数进行相加
                $ercount= $user->where(array('sid'=>$value['id']))->count();

            $erjicount +=$ercount;

        }
        $erjiidarr=array();*/


        /* $_SESSION['sanji']=$sj;
        $_SESSION['erji']=$erjirenshu;
        $_SESSION['yiji']=$yiji;


        $this->display();*/
        //$xiajiidarr = array_merge($erjiarr,$sanjiarr,$sijiarr);

        if (is_array($erjiarr)) {

            if (is_array($sanjiarr)) {

                if (is_array($sijiarr)) {
                    return array_merge($erjiarr, $sanjiarr, $sijiarr);
                } else {

                    return array_merge($erjiarr, $sanjiarr);
                }

            } else {
                return $erjiarr;
            }


        } else {
            return false;
        }


    }


    public function xiaji()
    {
        $user = D('user');
        $sj = 0;
        $erjirenshu = 0;
        $yiji = 0;


        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/xiaji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        /*if(empty($_GET['code'])){
            //$weObj->getOauthRedirect($reurl);
            redirect($weObj->getOauthRedirect($reurl));
        }else{
            //$code = $_GET['code'];
            $a =$weObj->getOauthAccessToken();
        }
        $chaxunarr =  $user->where(array('openid'=>$a['openid']))->find();
        $_SESSION['userinfo'] = $chaxunarr;*/

        //$id =$_SESSION['userinfo']['id'];
        $id = $_GET['id'];
        $yiji = $user->where(array('sid' => $id))->count();
        if ($yiji) {
            $yarr = $user->where(array('sid' => $id))->select();
            foreach ($yarr as $aaa) {

                $erjiarr[] = $aaa['id'];
                // $user->where(array('sid'=>$value['id']))->select();


            }

            $erjirenshu = $user->where(array('sid' => array('IN', $erjiarr)))->count();//本质是二级的个数
            if ($erjirenshu) {


                $sanjiarrbbb = $user->where(array('sid' => array('IN', $erjiarr)))->select();//
                foreach ($sanjiarrbbb as $sjarr) {
                    $sanjiarr[] = $sjarr['id'];
                }

                $sj = $user->where(array('sid' => array('IN', $sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数


            } else {


            }


        } else {


        }


        /*$erjicount=0;

        foreach($yarr as $value){ //循环出每个人的下级数进行相加
                $ercount= $user->where(array('sid'=>$value['id']))->count();

            $erjicount +=$ercount;

        }
        $erjiidarr=array();*/


        $_SESSION['sanji'] = $sj;
        $_SESSION['erji'] = $erjirenshu;
        $_SESSION['yiji'] = $yiji;
        $jibie = $this->daili($_SESSION['userinfo']['jibie']);
        $this->assign('jibie', $jibie);


        $this->display();


    }

    public function yijihuiyuan()
    {
        $xiaoxi = new \Home\Controller\XiaoxiController();
        $xiaoxi->weixincheck();
        $id = $_SESSION['userinfo']['id'];
        $user = D('user');

        $yijiarr = $user->where(array('sid' => $id))->select();//查询到所有的一级会员id
        $this->assign('fanhuiarr', $yijiarr);

        $this->display();


    }

    public function erjihuiyuan()
    {

        $user = D('user');


        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/xiaji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $id = $_SESSION['userinfo']['id'];
        $user = D('user');
        $yijiarr = $user->where(array('sid' => $id))->select();//查询到所有的一级会员id
        foreach ($yijiarr as $yijia) {
            $erjiid[] = $yijia['id'];
        }
        $erjifanhui = $user->where(array('sid' => array('IN', $erjiid)))->select();


        $this->assign('erjifanhui', $erjifanhui);

        $this->display();
    }

    function sanjihuiyuan()
    {

        $sj = 0;
        $erjirenshu = 0;
        $yiji = 0;
        $user = D('user');


        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/xiaji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }


        $id = $_SESSION['userinfo']['id'];
        $yiji = $user->where(array('sid' => $id))->count();
        if ($yiji) {


            $yarr = $user->where(array('sid' => $id))->select();

            //$erjiidarr=array();
            foreach ($yarr as $aaa) {

                $erjiarr[] = $aaa['id'];
            }

            $erjirenshu = $user->where(array('sid' => array('IN', $erjiarr)))->count();//本质是二级的个数
            if ($erjirenshu) {

                $sanjiarrbbb = $user->where(array('sid' => array('IN', $erjiarr)))->select();//
                foreach ($sanjiarrbbb as $sjarr) {
                    $sanjiarr[] = $sjarr['id'];
                }
                $sj = $user->where(array('sid' => array('IN', $sanjiarr)))->count();//三级的个数 直接传入所有二级用户的id查找三级个数

                if ($sj) {

                    $sjarr = $user->where(array('sid' => array('IN', $sanjiarr)))->select();
                }


            }


        }


        $_SESSION['sanji'] = $sj;
        $_SESSION['erji'] = $erjirenshu;
        $_SESSION['yiji'] = $yiji;
        $this->assign('sjarr', $sjarr);


        $this->display();


    }

    function yanzhengma()
    {

        $Verify = new \Think\Verify();


        $Verify->codeSet = '0123456789';
        $Verify->fontSize = 10;
        $Verify->length = 3;
        $Verify->useCurve = false;
        $Verify->useNoise = false;


        $Verify->entry();

    }

    /*帮助界面*/
    public function bangzhu()
    {
        $this->display();
    }

    /*
     * 申请提现*/
    public function tixian1()
    {
        /*$this->error('系统正在升级提现模块,请稍等');
        exit;*/
        $openid = $_GET['openid'];
        $user = M('user');
        $userarr = $user->where(array('openid' => $openid))->find();
        $month = date('m');
        $year = date('Y');
        $last_month = date('m') - 1;

        if($month == 1){
            $last_month = 12;
            $year = $year - 1;
        }
        $beginTime=mktime(0, 0, 0, $last_month, 1, $year);
        $endTime=mktime(0, 0, 0, $month, 1, $year);
        $where['time']=array('between',array($beginTime,$endTime));
        $where['openid']=$openid;
        M('yongjinjilu')->where($where)->sum('jiner');
        $tixian = M('tixian');
        $ti = $tixian->where(array('openid' => $openid))->find();
        if ($ti['status'] = 1) {
            $ztx = $userarr['money'];//提现中的金额
            $this->assign('ztx', $ztx);
        } else {
            if ($ti) {
                $ztx = $ti['money'];//提现中的金额
                $this->assign('ztx', $ztx);
            } else {
                $ztx = '0.00';//提现中的金额
                $this->assign('ztx', $ztx);
            }
        }

        $this->assign('userarr', $userarr);

        $this->display();
    }

    public function tixian()
    {
        $this->error('系统正在升级提现模块,请稍等');
        exit;

        $tixian = D('tixian');
        $user = D('user');

        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/tixian/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        //判断用户有咩有验证手机
        //  $shoujiarr = $user->where(array('openid'=>$_SESSION['userinfo']['openid']))->find();
        //var_dump(count($shoujiarr['tel']));
        //exit;
        // $b=strlen($_SESSION['userinfo']['tel']);

        if (strlen($_SESSION['userinfo']['tel']) < 5) {
            $this->redirect('Wx/tishix');
            exit;
        }


        $barr = $tixian->where(array('openid' => $_SESSION['userinfo']['openid']))->max('id');//找到用户最近一次提现记录的id值
        if (!isset($barr)) {
            //$user = D('user');
            $jinbiarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->field('jinbi')->find();
            $this->assign('jinbiarr', $jinbiarr);
            $this->display();

        } else {
            $carr = $tixian->where(array('id' => (int)$barr))->find();//找到用户最近一次提现的所有记录
            $status = $carr['status'];//用户的提款状态 0代表正在提款中,1代表已经提款完毕
            if ($status == 0) {

                $this->display('dengdai');
                exit;
            } else {
                $jinbi = $_GET['yongjin'];
                //判断用户有多少金币
                $user = D('user');
                $jinbiarr = $user->where(array('openid' => $_SESSION['userinfo']['openid']))->find();
                $jinbiarr['jinbi'] = $_GET['yongjin'];

                $this->assign('jinbiarr', $jinbiarr);
                $this->assign('dingdanhao', $_GET['dingdanhao']);
                $this->display();
            }

        }


    }

    public function dengdai()
    {
        $this->display();

    }

    public function sed($canshu, $zuozhe)
    {

        $op = array(
            'account' => 'ruisisoft@163.com',
            'password' => 'ruisisoft'


        );
        $options = array(
            'token' => 'zzruisi', //填写你设定的key
            'encodingaeskey' => C(ENCO), //填写加密用的EncodingAESKey
            'appid' => C(APPID), //填写高级调用功能的app id
            'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
        );
        $weObj = new Util\Wechatnew($options);
        $userinfo = $weObj->getUserInfo($zuozhe);
        $content = '用户' . $userinfo['nickname'] . '通过你的二维码关注了公众号';

        $ko = new Util\Wechatext($op);
        $ko->send($canshu, $content);


    }

    public function jilu()
    {
        $openid = $_SESSION['userinfo']['openid'];
        $tixian = D('tixian');
        $fanhuiarr = $tixian->where(array('openid' => $openid))->select();


        $this->assign('fanhuiarr', $fanhuiarr);


        $this->display();

    }

    public function tishix()
    {
        /*  $yuanyin = $_GET['jishu'];
        $this->assign('yuanyin',$yuanyin);*/
        $this->display();

    }

    public function shoujiyemian()
    {
        $id = $_GET['id'];
        $user = D('user');
        $arr = $user->where(array('id' => $id))->find();

        if (empty($arr['tel'])) {
            $this->display();
        } else {
            $this->redirect('bdshouji');
            exit;
        }


    }

    public function duanxin()
    {
        return rand(100000, 999999);

    }

    public function fasongyanzhengma()
    {
        $dianhua = $_GET['dianhua'];
        $duanxin = $this->duanxin();
        $dx = D('duanxin');

        $content = '【竹纺之家】验证码为:' . $duanxin . '(勿告诉他人)，请在页面中输入完成验证';


        $ch = curl_init();
        $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile=' . $dianhua . '&content=' . $content . '&tag=2';
        $header = array(
            'apikey: 66023c0fda2ad107941e49b5fd06c0bf',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch, CURLOPT_URL, $url);
        $res = curl_exec($ch);

        $resn = json_encode(array('duanxin' => $duanxin));

        echo $resn;


    }

    public function yanzhengsj()
    {

        $dh = $_GET['dh'];
        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/yanzhengsj/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        /* var_dump($_SESSION['userinfo']['id']);
        exit;*/
        $user->where('id=' . $_SESSION['userinfo']['id'])->save(array('tel' => $dh));

        $this->display('bdshouji');

    }

    public function bdshouji()
    {
        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/bdshouji/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        $this->display();
    }

    public function yanzhengx()
    {
        $this->display();
    }

    public function tixianxinxi()
    {
        $user = D('user');
        $dingdan = D('dingdan');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/tixianxinxi/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }
        $weitixianjinbi = $_SESSION['userinfo']['jinbi'];
        $tixian = D('tixian');
        $yijing = 0;
        $shijianpanduan = $dingdan->where(array('sjid' => $_SESSION['userinfo']['id'], 'status' => 1, 'shouhuo' => 0))->select();
        if ($shijianpanduan) {
            if ($shijianpanduan['zhifushijian'] + 1294000 < time()) {//支付时间过15天
                foreach ($shijianpanduan as $panduana) {
                    $this->shijianshouhuo($panduana['dingdanhao']);

                }
            }

        }

        if ($jinarr = $tixian->where(array('openid' => $_SESSION['userinfo']['openid']))->select()) {
            foreach ($jinarr as $key => $jinbia) {
                $yijing += $jinbia['jiner'];//已提现佣金总额 (包含已支付和未支付)

            }
        }


        //提现中金额
        $tixianzhonga = 0;
        if ($tixianzhongarr = $tixian->where(array('openid' => $_SESSION['userinfo']['openid'], 'status' => 0))->select()) {
            foreach ($tixianzhongarr as $pp) {
                $tixianzhonga += $pp['jiner'];//tixianzhong

            }
        }
        $yijinga = 0;
        if ($yijingaarr = $tixian->where(array('openid' => $_SESSION['userinfo']['openid'], 'status' => 1))->select()) {
            foreach ($yijingaarr as $ppa) {
                $yijinga += $ppa['jiner'];//yjingtixian

            }
        }

        //

        //计算可提现金币
        //计算已确认金币
        $yiqueren = $_SESSION['userinfo']['jinbiquere'];

        preg_match_all('#\(([^\(]*)\)#', $yiqueren, $yiquerenarr);//获取所有的已确认订单 dingdanhao-jine

        $ddarr = $yiquerenarr[1];//已确认订单的数组

        $yqrjb = 0;//已经确认的金额
        foreach ($ddarr as $darr) {
            $fg = explode('-', $darr);//分割后的值
            $dingdanhaoarr[] = $fg[0];//已确认订单号数组
            $yqrjb += $fg[1];//已确认佣金总额
        }
        //计算可提现佣金
        $ketixian = 0;
        if ($dingdanhaoarr) {

            $where['dingdanhao'] = array('in', $dingdanhaoarr);
            $where['shouhuoshijian'] = array('ELT', time() - 864000);//判断是不是超过十天

            $ktxarr = $dingdan->where($where)->select();


            $ktxa = "";
            foreach ($ktxarr as $txarr) {
                $ktxa .= $txarr['dingdanhao'] . '-';//得到可提现订单的订单号并且存入数组$ktxa

                //array_search($txarr['dingdanhao'],$ddarr);
                foreach ($ddarr as $darr) {
                    $fg = explode('-', $darr);//分割后的值


                    if (in_array($txarr['dingdanhao'], $fg)) {


                        $ketixian += $fg[1];//这就是可提现金额
                    }

                }


            }
        }
        //$weitixianjinbi就是未确认金币
        $mmm = $weitixianjinbi + $yijing;//全部推广金币
        $ceshi = $ketixian + $yijing;//已确认的金币

        //已提现佣金总额
        $this->assign('tixianzhonga', $tixianzhonga);
        $this->assign('weiqueren', $weitixianjinbi - $ketixian);
        $this->assign('yijing', $yijinga);
        $this->assign('ketixian', $ketixian);
        $this->assign('yqrjb', $ceshi);
        $this->assign('ktxa', $ktxa);
        $this->assign('mmm', $mmm);


        $this->display();


    }

    public function shijianshouhuo($dingdanhao)
    {

        $user = D('user');
        $dingdan = D('dingdan');
        $dd = $dingdan->where(array('dingdanhao' => $dingdanhao))->find();

        $dingdan->where(array('dingdanhao' => $dingdanhao))->save(array('shouhuo' => 1, 'shouhuoshijian' => time()));

        //改状态 改成收货状态
        //找到上级的三人
        $ddarr = $dingdan->where(array('dingdanhao' => $dingdanhao))->find();

        $id = $ddarr['sjid'];//找到下单id
        $benjiarr = $user->where(array('id' => $id))->find();//找到上级id

        $yijiarr = $user->where(array('id' => $benjiarr['sid']))->find();


        if ($yijiarr && $yijiarr['id'] > 1) {
            //找到上上一级id
            $yj = $ddarr['yijiyongjin'];
            //之前的 $yijiarr['jinbiquere']
            //存储订单号和金额
            $cunchun1 = $yijiarr['jinbiquere'] . '(' . $dingdanhao . '-' . $yj . ')';
            $user->where(array('id' => $yijiarr['id']))->save(array('jinbiquere' => $cunchun1));

            $erjiarr = $user->where(array('id' => $yijiarr['sid']))->find();
            if ($erjiarr) {
                $ej = $ddarr['erjiyongjin'];
                $cunchun2 = $erjiarr['jinbiquere'] . '(' . $dingdanhao . '-' . $ej . ')';
                $user->where(array('id' => $erjiarr['id']))->save(array('jinbiquere' => $cunchun2));


                $sanjiarr = $user->where(array('id' => $erjiarr['sid']))->find();
                if ($sanjiarr) {


                    $sj = $ddarr['sanjiyongjin'];
                    $cunchun3 = $sanjiarr['jinbiquere'] . '(' . $dingdanhao . '-' . $sj . ')';
                    $user->where(array('id' => $sanjiarr['id']))->save(array('jinbiquere' => $cunchun3));

                }
            }
        }


    }

    public function dotixian()
    {
        $xiaoxi = new \Home\Controller\XiaoxiController();

        $user = D('user');
        if (empty($_SESSION['userinfo'])) {
            $options = array(
                'token' => 'zzruisi', //填写你设定的key
                'encodingaeskey' => 'encodingaeskey', //填写加密用的EncodingAESKey
                'appid' => C(APPID), //填写高级调用功能的app id
                'appsecret' => C(APPSECRET) //填写高级调用功能的密钥
            );
            $weObj = new Util\Wechatnew($options);
            $reurl = C(URL) . U('Home/Wx/dotixian/');

            if (empty($_GET['code'])) {
                //$weObj->getOauthRedirect($reurl);
                redirect($weObj->getOauthRedirect($reurl));
            } else {
                //$code = $_GET['code'];
                $a = $weObj->getOauthAccessToken();
            }
            $chaxunarr = $user->where(array('openid' => $a['openid']))->find();
            $_SESSION['userinfo'] = $chaxunarr;
        }

        //先判断电话
        $openid = $_POST['openid'];
        $tixian = D('tixian');
        // $tixian->where(array('openid'=>$openid))->max('id');
        $barr = $tixian->where(array('openid' => $openid))->max('id');//找到用户最近一次提现记录的id值

        //$tixian->where()->
        if ($barr) {
            $carr = $tixian->where(array('id' => (int)$barr))->find();//找到用户最近一次提现的所有记录
            $status = $carr['status'];//用户的提款状态 0代表正在提款中,1代表已经提款完毕
            if ($status == 0) {

                $this->display('dengdai');
                exit;
            }

        }


        /* $uarr =  $user->where(array('openid'=>$openid,'tel'=>$tel))->find();
        if(!$uarr){
            $this->redirect('yanzhengx');
            exit;

        }*/

        $_POST['addtime'] = time();

        $tixian = D('tixian');
        $userarr = $user->where(array('openid' => $openid))->find();
        $money = $userarr['jinbi'];
        if ($_POST['money'] > 0 && $_POST['money'] <= $money) {

            if ($tixian->data($_POST)->add()) {

                $user->where(array('openid' => $_SESSION['userinfo']['openid']))->setDec('jinbi', $_POST['money']);
                $user->where(array('openid' => $_SESSION['userinfo']['openid']))->save(array('money' => $_POST['money'], 'tixianstatus' => 1));
                //$tixian->where(array('openid'=>$_SESSION['userinfo']['openid']))->setDec('jiner',$_POST['money']);
                $xiaoxi->addxiaoxi($_POST['openid'], '提现');
                $this->display('tishi3');
            };

        } else {
            $xiaoxi->addxiaoxi($_POST['openid'], '重复提现');
            $this->display('tishi4');
        }
    }

    /*
     ** 申请信息查询
    */
    public function yshenqingcx()
    {
        //echo $_GET['id'];
        $userid = $_GET['id'];
        $user = M('user');
        $alluser = $user->where(array('sid' => $userid))->field('id,jibie,shenhe,nickname,name')->select();
        $this->assign('userid', $userid);
        $this->assign('alluser', $alluser);

        $this->display();
    }

    public function yshenqingreninfo()
    {
        $id = $_GET['id'];
        $userid = $_GET['userid'];


        $user = M('user');
        $userarr = $user->where(array('id' => $userid))->field('openid,nickname,jibie,headimgurl')->find();//用户信息
        $shenhearr = $user->where(array('id' => $id))->field('id,nickname,name,cart,weixinhao,tel,province,city,sex,shenhe')->find();//用户信息
        $jibie = $this->daili($userarr['jibie']);
        $dizhi = M('dizhi');
        $maxid = $dizhi->where(array('openid' => $userarr['openid']))->max('id');
        $dizhiarr = $dizhi->where(array('id' => $maxid))->find();
        $this->assign('list', $dizhiarr);
        $this->assign('jibie', $jibie);
        $this->assign('userarr', $userarr);
        $this->assign('shenhearr', $shenhearr);
        $this->display();

    }

    /*
     * 给下级打款
    */
    public function geixiajidakuan()
    {
        $user = D('user');
        $where['sid'] = $_SESSION['userinfo']['id'];
        //var_dump($where);
        $myxiaji = $user->where($where)->select();
        if ($myxiaji != "") {
            foreach ($myxiaji as $k => $value) {
                $myxiaji[$k]['jibie'] = $this->daili($value['jibie']);
            }
        }
        $this->assign('xjlist', $myxiaji);
        $this->display('dlr_list');
    }

    /*
     * 打款页面
     */
    public function dakuan()
    {
        $user = D('user');
        $kucun = $user->where('id=' . $_SESSION['userinfo']['id'])->getField('heshu');
        $id = $_GET['id'];
        if ($id == "") {
            $this->error('未获取下级id！');
        } else {
            $this->assign('id', $id);
            $this->assign('kucun', $kucun);
            $this->display();
        }
    }

    /*
	 * 保存打款数据
	 */
    public function savedakuan()
    {
        $user = D('user');
        $dakuanjilu = D('dakuanjilu');
        $where['id'] = $_POST['id'];
        $heshu = $_POST['heshu'];

        $statut1 = $user->where($where)->setInc('heshu', $heshu);
        //减自己
        $statut2 = $user->where('id=' . $_SESSION['userinfo']['id'])->setDec('heshu', $heshu);
        if ($statut1 === 1 && $statut2 === 1) {
            $data['dakuanren'] = $_SESSION['userinfo']['id'];
            $data['shoukuanren'] = $_POST['id'];
            $data['heshu'] = $heshu;
            $data['dakuanshijian'] = time();
            $statut3 = $dakuanjilu->add($data);
            if ($statut3) {

                //页面重定向
                $this->redirect('Home/User/tishi');
            } else {

                //页面重定向
                $this->redirect('Home/User/error/type/1');
            }

        } else {
            $this->redirect('Home/User/error/type/2');

        }


    }

    /**
     * 错误提示
     */
    public function error2()
    {
        $type = $_GET['type'];
        if ($type == 1) {
            $tishi = '请填写完整信息！';
        } else if ($type == 2) {
            $tishi = '操作失败！';
        }
        $this->assign('tishi', $tishi);
        $this->display();
    }

    public function shangcheng_index()
    {
        $this->display('');
    }

    public function sc_index()
    {

        $flash = M('flash');
        $tu = $flash->select();
        $this->assign('tu', $tu);
        $chanpin = M('chanpin');
        $shangpin = $chanpin->where('sid=9')->order('paixu')->select();
        $this->assign('shangpin', $shangpin);
        $this->display('');
    }
    public function sc_gonggao(){
        $wen = M('article');
        $gonggao = $wen->where(array('sid'=>18,'aid'=>53))->find();
        $this->assign('gonggao', $gonggao);
        $this->display();
    }

    public function spxq()
    {
        $chanpin = M('chanpin');
        $where['aid'] = I("aid");
        $shangpin = $chanpin->where($where)->find();
        $this->assign('shangpin', $shangpin);
        $comment=M('comment');
        $where1['goods_id'] = I("aid");
        $pinglun=$comment->where($where1)->order('add_time desc')->select();
        //var_dump($pinglun);
        foreach ($pinglun as $k=>$v){
            //$xingxi=$v[''];
            //var_dump($xingxi);
            $openid=$v['user_id'];
            $userarr=M('user')->where(array('openid'=>$openid))->find();
            $xiang=$userarr['headimgurl'];
            $this->assign('xiang',$xiang);
            $this->assign('pinglun',$pinglun);
        }

        $this->display();
    }

    public function duihuan()
    {
        $openid = $_GET['openid'];
        $list = M('dizhi')->where(array('openid' => $openid))->find();
        $user = M('user')->where(array('openid' => $openid))->find();
        if ($list) {
            $aid = $_GET['aid'];
            $chanpin = M('chanpin')->where(array('aid' => $aid))->find();
            $this->assign('list', $list);
            $this->assign('user', $user);
            $this->assign('chanpin', $chanpin);
            $this->display('shouhuo');
        } else {
            $this->display();
        }

    }
    public function doshou(){
        $data['openid']=$_GET['openid'];
        $data['shoujianren']=isset($_POST['name']) ? trim($_POST['name']) : '';
        $data['dianhua']=isset($_POST['tel']) ? trim($_POST['tel']) : '';
        $data['diqu'] = isset($_POST['diqu']) ? trim($_POST['diqu']) : '';
        $data['xiangxi'] = isset($_POST['xiangxi']) ? trim($_POST['xiangxi']) : '';
        $data['card']= isset($_POST['cart']) ? trim($_POST['cart']) : '';
        $data['weixinhao']= isset($_POST['weixinhao']) ? trim($_POST['weixinhao']) : '';
        $hang=M('dizhi')->add($data);
        if($hang){
            $this->redirect('User/duihuan');
        }

    }

    /*
     * 订单详情入库*/
    public function doadd()
    {
        $openid = $_GET['openid'];
        //$list=M('dizhi')->where(array('openid'=>$openid))->find();
        $user = M('user')->where(array('openid' => $openid))->find();
        $jinbi = $user['jinbi'];
        $jifen=$user['jifen'];
        $aid = $_GET['aid'];
        $chanpin = M('chanpin')->where(array('aid' => $aid))->find();
        $title=$chanpin['title'];
        $price = $chanpin['price'];
        $shuliang = $_POST['shuliang'];
        if ($jifen >= $price * $shuliang) {
            $user = M('user');
            $userarr = $user->where(array('openid' => $openid))->field('nickname,headimgurl,openid,sid')->find();
            $data['nickname'] = $userarr['nickname'];
            $data['headimgurl'] = $userarr['headimgurl'];
            $data['openid'] = $userarr['openid'];
            $data['sjid'] = $userarr['sid'];
            $data['addtime'] = time();
            $data['price']=$price;
            $data['title']=$title;
            $data['dingdanhao']=date('YmdHis').rand(1000,9999);
            $data['aid'] = isset($_GET['aid']) ? trim($_GET['aid']) : '';
            //$data['id']= isset($_POST['id'])?intval($_POST['id']):0;
            $data['shoujianren'] = isset($_POST['name']) ? trim($_POST['name']) : '';
            $data['dianhua'] = isset($_POST['tel']) ? trim($_POST['tel']) : '';
            $data['shuliang'] = isset($_POST['shuliang']) ? trim($_POST['shuliang']) : '';
            $data['diqu'] = isset($_POST['diqu']) ? trim($_POST['diqu']) : '';
            $data['xiangxi'] = isset($_POST['xiangxi']) ? trim($_POST['xiangxi']) : '';
            $data['youbian'] = isset($_POST['youbian']) ? trim($_POST['youbian']) : '';
            if ($data['shoujianren'] && $data['dianhua'] && $data['shuliang'] && $data['diqu'] && $data['xiangxi'] && $data['youbian']) {
                $dingdan = M('dingdan');

                $hang = $dingdan->add($data);
                M('dizhi')->where(array('openid'=>$openid))->save(array('youbian'=>$data['youbian']));
                if ($hang) {
                    M('user')->where(array('openid'=>$openid))->save(array('jifen'=>$jifen-$price));
                    M('chanpin')->where(array('aid'=>$aid))->setInc('yishou',$data['shuliang']);
                    M('chanpin')->where(array('aid'=>$aid))->setDec('kucun',$data['shuliang']);
                    $this->display('tishi6');
                }
            }else{
                $this->display('tishi8');
            }


        } else {
            $this->display('tishi5');
        }
    }

    /*
     * 我的订单*/
    public function dingdan()
    {
        $openid = $_GET['openid'];
        $dingdan = M('dingdan')->where(array('openid' => $openid))->order('addtime desc')->select();
        if($dingdan){
            foreach ($dingdan as $k => $v) {
                $aid = $v['aid'];
                $chanpin = M('chanpin')->where(array('aid' => $aid))->find();
                $dingdan[$k]['chanpin'] = $chanpin;
            }

            //var_dump($dingdan);


            //var_dump($chanpin);
            //$this->assign('chanpin',$chanpin);
            $this->assign('dingdan', $dingdan);
            $this->display();
        }else{
            $this->display('dingdan2');
        }

    }

    /*
     * 评价*/
    public function pingjia()
    {
        $openid = $_GET['openid'];
        $dingdan = M('dingdan')->where(array('openid' => $openid))->order('addtime desc')->select();
        $aid = $_GET['aid'];
        $chanpin = M('chanpin')->where(array('aid' => $aid))->find();

        $this->assign('chanpin', $chanpin);
        $this->assign('dingdan', $dingdan);
        $this->display();
    }

    /*
     * 提交评价*/
    public function dopj()
    {
        $openid = $_GET['openid'];
        $aid=$_GET['aid'];
        $id=$_GET['id'];

        $userarr = M('user')->where(array('openid' => $openid))->find();
        $data['goods_id'] = isset($_GET['aid']) ? trim($_GET['aid']) : '';
        $data['content'] = isset($_POST['content']) ? trim($_POST['content']) : '';
        $data['username'] = $userarr['nickname'];
        $data['tel'] = $userarr['tel'];
        $data['add_time'] = time();
        $data['user_id'] = $openid;
        $pingjia = M('comment');

        $hang = $pingjia->add($data);
        if ($hang) {
            M('dingdan')->where(array('openid'=>$openid,'aid'=>$aid,'id'=>$id))->save(array('stutus'=>1));
            $this->display('tishi6');

        }

    }
    public function qrss(){
        $openid=$_GET['openid'];
        $aid=$_GET['aid'];
        $id=$_GET['id'];
        M('dingdan')->where(array('openid'=>$openid,'aid'=>$aid,'id'=>$id))->save(array('pingjia'=>1));
        $this->display('tishi7');
    }
}