/**
 * API-Rest 框架调用封装
 *
 * @author lsa
 * @date 2017.02.16
 * @date 2017.12.18
 */
;
(function(window){

    // API基类
    function API()
    {
        var self = this;

        // this.secret_3des = 'my.oschina.net/penngo?#@'; // 3des 加解密 密钥
        // this.iv_3des = '01234567'; // 3des

        this.host = '';
        this.access_key = 'LAPtXJMN1FImCLQa';
        this.access_secret = 'oT953JxnSHijGuRkhvFalAb7OVrdga';
        this.access_token = localStorage.getItem('Access-Token');

        this.headers = {
            'Access-Token': this.access_token,
            'Access-Key': this.access_key,
            'debug': 1
        };

        $.ajax({
            url: this.host + '/get_timestamp',
            type: 'get',
            headers: this.headers,
            success: function(res, msg, xhr)
            {
                // res = self.resolve(res);

                if(res.status.code === '00000')
                {
                    var timestamp = (new Date()).getTime(); // 当前时间戳
                    self.time_diff = res.result.timestamp - timestamp;

                    self.access_token = xhr.getResponseHeader('Access-Token');
                    localStorage.setItem('Access-Token', self.access_token);
                }
            }
        });
    }

    // 数据排序
    API.prototype.data_sort = function(params)
    {
        if(typeof params === 'object')
        {
            var t = {};
            if(params instanceof FormData)
            {
                params.forEach(function(v, k){
                    if(typeof v === 'string'){
                        t[k] = v;
                    }
                });
            }else{
                t = params;
            }

            var keys = Object.keys(t).sort(),
                obj = {};
            for (var i = 0; i < keys.length; i++) {
                obj[keys[i]] = t[keys[i]];
            }
            return obj;
        }
        return false;
    };

    // 生成签名密钥
    API.prototype.create_sign = function(obj)
    {
        var params = this.data_sort(obj);

        if(params)
        {
            var str = '';
            for(var k in params){
                var item = params[k];
                str += k + '=' + $.md5(String(item)) + '&';
            }
            str = str.substring(0, str.length-1); // 去除 &
            return $.sha1(str + this.access_secret); // 签名
        }
        return false;
    };

    // 发起请求
    API.prototype.request = function(setting)
    {
        var timestamp = new Date().getTime(); // 当前时间戳

        if(typeof setting.data !== 'undefined') {
            if(setting.data instanceof FormData){
                setting.data.append('_t', timestamp + this.time_diff);
            }else{
                setting.data._t = timestamp + this.time_diff;
            }
        }else{
            setting.data = {
                '_t': timestamp + this.time_diff
            };
        }
        this.headers.Authorizations = this.create_sign(setting.data);

        if(typeof setting.headers === 'object'){
            setting.headers = $.extend({}, this.headers, setting.headers);
        }else{
            setting.headers = $.extend({}, this.headers);
        }

        $.ajax(setting);
    };

    // Resolve Response
    API.prototype.resolve = function(res)
    {
        return JSON.parse(this.decrypt(res));
    };

    // 3des 解密
    API.prototype.decrypt = function(res)
    {
        var genKey = genkey(this.secret_3des, 0, 24);
        return des(genKey.key, atob(res), 0, 1, this.iv_3des, 1);
    };

    // 3des 加密
    API.prototype.encrypt = function(res)
    {
        var genKey = genkey(this.secret_3des, 0, 24);
        return btoa(des(genKey.key, res, 1, 1, this.iv_3des, 1));
    };

    window.API = new API();
})(window);