/**
 * Cookie v1.0.1 | (c) 2014 MoFend Network Tec. Ltd. Co.
 *
 * This class is designed to manage cookies by the javascript.
 * It has two parameters use to configure the cookie.
 *
 * Parameter 'expire' is the life of cookies, it can use in full file.
 * Parameter 'prefix' is the prefix name of cookies, it used same as the 'expire'.
 *
 * Function setCookie can set your cookie, you just need to give it your cookie's name and value.
 * If you still want your cookie has a different expire( or prefix ) with other cookies, you can
 * also give it to set when you use it.
 *
 * Function getCookie will find the value of the cookie what you want, and you can just send it's name.
 *
 * Function clearCookie can clear all of the cookies, it uses the deleteCookie which is a function
 * delete cookies by name.
 *
 * Function getCookieNames can give you all of the cookies' names, and getCookiesValues can give you
 * all of the values without name.
 *
 * @author H.q. Wang <karl-ford@hotmail.com>
 *
 */

var Cookie = {
    expire : 0,   // the life of all of the cookies
    prefix : '',  // the prefix name of all of the cookies
    path   : '/', // the cookie use range, default is for all
    /**
     * set a cookie by the function
     *
     * name   : name of the cookie u want to set | type : string
     * value  : value of the cookie u want to set | type : mixed
     * expire : the life time of the cookie u want to set, count by seconds | type : integer
     * prefix : the prefix of the cookie u want to set | type : mixed
     *
     * return boolean
     */
    setCookie : function(name, value, expire, prefix, path){
        expire = expire || Cookie.expire;
        prefix = prefix || Cookie.prefix;
        path   = path || Cookie.path;

        // if the value equals null, delete cookie whose name is prefix + name and return false
        if(value == null){
            Cookie.deleteCookie(name, prefix);
            return false;
        }

        if(name.legth == 0){
            return false;
        }

        var cookie = prefix + name + '=' + escape(value);
        if(expire > 0){
            var date = new Date();
            date.setTime(date.getTime() + expire * 1000);
            cookie += ';expires=' + date.toGMTString();
        }

        cookie += ';path=' + path;

        return (document.cookie = cookie) ? true : false;
    },

    /**
     * get the value of cookie what u want by name
     *
     * name : name of the cookie u want to get value, don't plus the prefix
     *
     * return mixed
     */
    getCookie : function(name, prefix){
        prefix = prefix || Cookie.prefix;

        var cookies = document.cookie.split("; ");
        var i = 0;
        var rs = false;
        for(i = 0; i < cookies.length; i++){
            var map = cookies[i].split('=');
            if(map[0] == prefix + name){
                rs = unescape(map[1]);
                break;// if find the cookie, break for and return result
            }
        }

        return rs;
    },

    /**
     * delete a cookie what u want by name
     *
     * name : name of the cookie u want to delete, don't plus the prefix
     *
     * return boolean
     */
    deleteCookie : function(name, prefix, path){
        prefix = prefix || Cookie.prefix;
        path   = path || Cookie.path;

        var date = new Date();
        date.setTime(-10000);
        var cookie = prefix + name + '=' + escape(null) + ';expires=' + date.toUTCString() + ';path=' + path;

        return (document.cookie = cookie) ? true : false
    },

    /**
     * delete all of the cookies
     *
     * return boolean
     */
    clearCookies : function(prefix){
        prefix = prefix || Cookie.prefix;

        var names = Cookie.getCookieNames(prefix);
        var rs = false;
        for(index in names)
            rs = Cookie.deleteCookie(names[index], prefix);

        return rs;
    },

    /**
     * get all of the cookies' names and return them in a array
     *
     * return array
     */
    getCookieNames : function(prefix){
        prefix = prefix || Cookie.prefix;

        var cookies = document.cookie.split("; ");
        var i = 0;
        var names = [];

        for(i = 0; i < cookies.length; i++){
            var map = cookies[i].split('=');
            if(prefix.length > 0){
                if(map[0].substr(0, prefix.length) == prefix){
                    names.push(map[0].substr(prefix.length, map[0].length));
                }
            }else
                names.push(map[0]);
        }

        return names;
    },

    /**
     * get all of the values without name
     *
     * return array
     */
    getCookieValues : function(prefix){
        prefix = prefix || Cookie.prefix;

        var cookies = document.cookie.split("; ");
        var i = 0;
        var values = [];

        for(i = 0; i < cookies.length; i++){
            var map = cookies[i].split('=');
            if(prefix.length > 0){
                if(map[0].substr(0, prefix.length) == prefix){
                    values.push(map[1]);
                }
            }else
                values.push(map[1]);
        }

        return values;
    }
}