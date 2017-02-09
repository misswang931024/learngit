<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";

    }

}

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody($_POST['shoujianren']);
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee($_POST['total']);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';


$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付样例-支付</title>
	<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">

	<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>

    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}
	function callpay()

	{
        /*$.ajax({
            type: 'POST',
            url: 'http://zhufang.ruisi.me/zhifu/example/jsapi2.php',
            // data to be added to query string:
            data: { 'shangpin': 'Zepto.js', 'total':100},
            // type of data we are expecting in return:
            dataType: 'json',
            timeout: 300,
            async:false,
            success: function(data){
                // Supposing this JSON payload was received:
                //   {"project": {"id": 42, "html": "<div>..." }}
                // append the HTML to context object.
                $.alert('aaa');
            },

        })*/
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;

				$("#shoujianren").val(res.userName);
                $("#dianhua").val(tel);
                $("#diqu").val(value1+value2+value3);
                $("#xiangxi").val(value4);
                $("#youbian").val(res.addressPostalCode);

				/*alert(value1 + value2 + value3 + value4 + ":" + tel);*/
			}
		);
	}
    window.onload = function(){

		if (typeof WeixinJSBridge == "undefined"){
			if( document.addEventListener ){
				document.addEventListener('WeixinJSBridgeReady', editAddress, false);
			}else if (document.attachEvent){
				document.attachEvent('WeixinJSBridgeReady', editAddress);
				document.attachEvent('onWeixinJSBridgeReady', editAddress);
			}
		}else{
			editAddress();
		}

	}
	
	/*window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};*/
	
	</script>
</head>
<body>
    <br/>



	<header class="bar bar-nav">
        <a class="icon icon-me pull-left open-panel"></a>
		<h1 class='title'>支付</h1>
	</header>

	<div class="content">

        <div class="list-block">
            <ul>
                <li class="item-content">
                    <div class="item-media"><i class="icon icon-f7"></i></div>
                    <div class="item-inner">

                        <div class="item-title">商品名称</div>
                        <div class="item-after">竹纺之家-大号竹制品</div>
                    </div>
                </li>

                <li class="item-content">
                    <div class="item-media"><i class="icon icon-f7"></i></div>
                    <div class="item-inner">
                        <div class="item-title">单价(元)</div>
                        <div class="item-after" id="danjia">50</div>
                    </div>
                </li>
                <li class="item-content">
                    <div class="item-media"><i class="icon icon-form-name"></i></div>
                <div class="item-inner">
                    <div class="item-title label">数量</div>
                    <div class="item-input" style="text-align: right">
                        <img id="add" style="width:30px;vertical-align: middle;padding-bottom: 2px" src="./jia.png"> <input type="tel" id="text_box" style="width:30px;display: inline" value="1" placeholder="Your name">件<img id="min" style="width:22px;vertical-align: middle;padding-left: 2px" src="./jian.png">
                    </div>
                </div>
                </li>
                <li class="item-content">
                    <div class="item-media"><i class="icon icon-f7"></i></div>
                    <div class="item-inner">
                        <div class="item-title">总计</div>
                        <div class="item-after" id="total">50元</div>
                    </div>
                </li>
            </ul>

        </div>

		<div class="list-block">
			<ul>
				<!-- Text inputs -->
				<li>
					<div class="item-content">
						<div class="item-media"><i class="icon icon-form-name"></i></div>
						<div class="item-inner">
							<div class="item-title label">收件人:</div>
							<div class="item-input">
								<input type="text" name="shoujianren" id="shoujianren" placeholder="Your name">
							</div>
						</div>
					</div>
				</li>

                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-password"></i></div>
                        <div class="item-inner">
                            <div class="item-title label">电话</div>
                            <div class="item-input">
                                <input type="tel" name="dianhua" id="dianhua" placeholder="telphone" class="">
                            </div>
                        </div>
                    </div>
                </li>


				<li>
					<div class="item-content">
						<div class="item-media"><i class="icon icon-form-email"></i></div>
						<div class="item-inner">
							<div class="item-title label">地区:</div>
							<div class="item-input">
								<input type="text" name="diqu" id="diqu" placeholder="dizhi">
							</div>
						</div>
					</div>
				</li>
                <li class="align-top">
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-comment"></i></div>
                        <div class="item-inner">
                            <div class="item-title label">详细地址:</div>
                            <div class="item-input">
                                <textarea id="xiangxi" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="item-content">
                        <div class="item-media"><i class="icon icon-form-password"></i></div>
                        <div class="item-inner">
                            <div class="item-title label">邮编:</div>
                            <div class="item-input">
                                <input type="tel" id="youbian" placeholder="" class="">
                            </div>
                        </div>
                    </div>
                </li>


				<!-- Date -->
			<!--	<li>
					<div class="item-content">
						<div class="item-media"><i class="icon icon-form-calendar"></i></div>
						<div class="item-inner">
							<div class="item-title label">生日</div>
							<div class="item-input">
								<input type="date" placeholder="Birth day" value="2014-04-30">
							</div>
						</div>
					</div>
				</li>-->
				<!-- Switch (Checkbox) -->
				<li>
					<div class="item-content">
						<div class="item-media"><i class="icon icon-form-toggle"></i></div>
						<div class="item-inner">
							<div class="item-title label">开关</div>
							<div class="item-input">
								<label class="label-switch">
									<input type="checkbox">
									<div class="checkbox"></div>
								</label>
							</div>
						</div>
					</div>
				</li>
				<!--<li class="align-top">
					<div class="item-content">
						<div class="item-media"><i class="icon icon-form-comment"></i></div>
						<div class="item-inner">
							<div class="item-title label">备注:</div>
							<div class="item-input">
								<textarea placeholder="备注"></textarea>
							</div>
						</div>
					</div>
				</li>-->
			</ul>
		</div>
		<div class="content-block">
			<div class="row">

				<div class="col-100"><a onclick="callpay()" class="button button-big button-fill button-success">微信支付</a></div>
			</div>
		</div>
	</div>









	<!--<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
		<button onclick="dizhi()">地址</button>
	</div>-->

</body>

<script>
    $(function(){
        //获得文本框对象
        var t = $("#text_box");
        //数量增加操作
        $("#add").click(function(){
            t.val(parseInt(t.val())+1)
            if (parseInt(t.val())!=1){
                $('#min').attr('disabled',false);
            }
            setTotal();
        })
        //数量减少操作
        $("#min").click(function(){
            if(parseInt(t.val())==1){
                $.alert('数量不能少于一件');
                return
            }
            t.val(parseInt(t.val())-1);
            if (parseInt(t.val())==1){
                $('#min').attr('disabled',true);
            }
            setTotal();
        })
        //计算操作
        function setTotal(){
            var danjia = $("#danjia").text();
            $("#total").html((parseInt(t.val())*parseInt(danjia)).toFixed(2));//toFixed()是保留小数点的函数很实用哦
        }
        //初始化
        setTotal();
    })
</script>
</html>