<div class="row">
    <div class="col-xs-12 col-sm-10 col-md-8 col-md-offset-2 col-sm-offset-1">
        <blockquote>
            <p>请求参数签名方式：</p>
        </blockquote>

        <div class="panel">
            <span>准备工作：</span>
            <ul>
                <li>取得Access-Key 和 Access-Secret</li>
            </ul>
        </div>

        <p>假设传输的数据是/test/test?name=admin&age=18<span class="text text-danger">（POST则为Body部分）</span></p>
        <ol>
            <li>
                <p>
                    添加<span class="label label-danger">UTC毫秒时间戳</span>到请求参数中。
                    <small class="text text-danger">时间戳以服务端为主！ 先取得服务端的时间戳，和本地时间戳计算差值，请求时加上差值</small>
                </p>
                <pre>name=admin&age=18&_t=1486530243589</pre>
            </li>
            <li>
                <p>按参数名(键名)，<span class="label label-danger">字符升序排序</span>，
                    并且对所有值(Value)进行<span class="label label-danger">md5</span>操作(int转string)
                </p>
                <pre>_t=MD5("1486530243589")&age=MD5("18")&name=MD5("admin")</pre>
            </li>
            <li>
                <p>把<span class="label label-danger">Access-Secret</span>添加到字符串结尾，然后进行<span class="label label-danger">sha1加密</span>，生成签名字符串(Authorizations)</p>
                <pre><span class="text-success">Authorizations</span> = sha1("_t=9000ce1df2e9489697b9af364e2f1aad&age=6f4922f45568161a8cdf4ad2299f6d23&name=21232f297a57a5a743894a0e4a801fc3" + <span class="text-success">Access-Secret</span>)</pre>
            </li>
            <li>
                <p>
                    把<span class="label label-info">签名字符串</span>、
                    <span class="label label-info">Access-Key</span>、
                    <span class="label label-info">Access-Token</span>放入HTTP Header中
                    <small class="text-danger">(Access-Token通过登录接口返回，客户端需自己保存！)</small>
                </p>
                <p></p>
                <pre>
GET /test/test?_t=1486530243589&age=18&name=admin HTTP/1.1
Host: api.lgj.cn
<span class="text-warning">Authorizations: 61a053b286fcbba41750c222c7b05d5339840dca
Access-Key: LAPtXJMN1FImCLQa
Access-Token: 27281228-589c-2fa086fe-35d2c61d5b18</span></pre>
            </li>

            <li>
                <div class="alert alert-danger" role="alert">
                    签名注意：
                    <div>1. 计算sha1之前请确保接口与接入方的字符串编码一致，统一使用utf-8编码，如果编码方式不一致则计算出来的签名会校验失败。</div>
                    <div>2. 签名方式可参考 <code>web/statics/js/API-Rest.js</code> 中的JavaScript实现</div>
<!--                    <div>2. 在Java或PHP中，URLEncoder处理字符串会将空格编码成 + ，此处我们统一在encode后把 + 替换成 %20 然后进行sha1签名计算(JavaScript、IOS不存在此问题)</div>-->
<!--                    <div class="text-warning">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;原因为实现标准不同，此处统一采用《94年国际标准备忘录RFC 1738》的编码方式</div>-->
<!--                    <div class="text-warning">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PHP：使用rawurlencode 和 rawurldecode</div>-->
<!--                    <div class="text-warning">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Java：使用StringUtils.replace(字符串, "+", "%20");</div>-->
                </div>
            </li>
        </ol>
    </div>

    <div class="col-xs-12 col-sm-10 col-md-8 col-md-offset-2 col-sm-offset-1">
        <div class="panel panel-info">
            <div class="panel-heading">
                其他约定：
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12">
                        <p>1. 客户端需在Header中添加<span class="label label-info">App-Version</span>，告知服务端当前客户端的版本信息与操作系统</p>
                        <pre>App-Version: ios/2.0.0</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>