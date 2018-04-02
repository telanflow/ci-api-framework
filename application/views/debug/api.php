<div class="row">
    <div class="col-xs-12">
        <ol class="breadcrumb">
            <?php $get = $this->input->get();?>
            <li>
                <?php
                $arr = explode('.', $this->input->get('p'));
                $api_name = array_pop($arr);
                ?>
                <a href="<?php echo base_url('debug/package?v='.$version_name.'&p='.$get['p'].'&c='.$get['c']);?>">
                    <i class="fa fa-reply"></i>&nbsp;
                    返回
                </a>
            </li>
            <?php if(!empty($child_dir)):?>
                <li class="active"><?php echo $child_dir;?></li>
            <?php endif;?>

            <div class="dropdown navbar-right navbar-api">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    版本设置 (当前：<?php echo $version_name;?>) <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <?php foreach($version_list as $v):?>
                        <li <?php echo $v == $version_name ? 'class="active"' : '';?>>
                            <a href="<?php echo base_url('debug/package?v=' . $v);?>"><?php echo $v;?></a>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </ol>

        <div class="row">
            <div class="col-xs-5">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label>Package</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?php echo implode('/', array_map('strtolower', $package_list));?>"
                                       id="api-url"
                                       data-url="<?php echo $version_name . '/' . implode('/', array_map('strtolower', $package_list));?>"
                                       readonly>

                                <span class="input-group-btn">
                                    <span class="btn btn-success" id="api-method">
                                        <?php echo !empty($doc['method']) ? strtoupper(trim($doc['method'])) : '未知';?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <style type="text/css">
                        input{outline:none}
                        .input-params{
                            width: 100%;
                            background-color: #FFF;
                            font-size: 12px;
                            border: none;
                            border-radius: 0;
                            border-bottom: 1px solid #F1F1F1;
                            padding: 5px 5px;
                            color: #455a64;
                        }
                        .input-params:focus{
                            box-shadow: 0 0 0 0 #F47023!important;
                            border-top-width: 0;
                            border-left-width: 0;
                            border-right-width: 0;
                            border-color: #F47023!important;
                        }
                        .pl{padding-left: 0;}
                        .np{padding: 0;}
                    </style>

                    <div class="col-xs-12" style="margin-bottom: 20px; ">
                        <div class="form-group">
                            <label class="" style="margin-bottom: 20px;">数据发送区</label>

                            <?php foreach($class_info['parameter'] as $k => $param):?>
                            <form class="form-inline form-item" role="form">
                                <div class="form-group col-xs-1 np is-check" style="width: 35px;text-align: center;">
                                    <input class="check-params" type="checkbox" <?php if($param[1] == '必需')echo 'checked';?>>
                                </div>

                                <div class="form-group col-xs-4 col-md-2 pl">
                                    <input type="text" class="input-params" value="<?php echo trim($k);?>" name="key" autocomplete="off" placeholder="key">
                                </div>

                                <?php if($param[0] != 'file'):?>
                                    <div class="form-group col-xs-7 col-md-9 pl">
                                        <input type="text" class="input-params" name="value" autocomplete="off" placeholder="<?php echo $param[2];?>">
                                    </div>
                                <?php else:?>
                                    <div class="form-group col-xs-7 col-md-9 pl">
                                        <input type="file" class="input-params" style="outline: none;" name="value" autocomplete="off" placeholder="<?php echo $param[2];?>">
                                    </div>
                                <?php endif;?>
                            </form>
                            <?php endforeach;?>
                        </div>

                    </div>


                    <div class="col-xs-12">
                        <div class="form-group">
                            <label></label>
                            <div class="input-group">
                                <select class="form-control" id="api-host">
                                    <option value="<?php echo LOCAL_HOST;?>">locat develop (本地环境)</option>
                                    <?php $web_service = $this->config->item('rest_service');?>
                                    <?php foreach($web_service as $service):?>
                                        <option value="<?php echo trim($service['host']);?>"
                                            <?php $host = parse_url($service['host']); if($host['host'] == $_SERVER['HTTP_HOST']) echo 'selected';?>>
                                            <?php echo $service['title'];?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                                <span class="input-group-btn">
                            <button class="btn btn-default" type="button" data-toggle="tooltip" data-placement="left" data-original-title="发送请求数据到服务端" onclick="send($(this))">
                                <span class="glyphicon glyphicon-send"></span>
                            </button>
                        </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inport_json">发送数据参考格式</label>
                    <?php
                        $params = array();
                        foreach($class_info['parameter'] as $k=>$v){
                            if(isset($v[3]) && is_array($v[3])){
                                unset($v[3]);
                            }
                            $params[$k] = implode('  ', $v);
                        }
                    ?>
                    <textarea name="inport_json" class="form-control" rows="10" readonly><?php echo json_format($params);?></textarea>
                </div>
            </div>

            <div class="col-xs-7">
                <div class="form-group">
                    <label>运行参数</label>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <div class="input-group-addon">运行状态</div>
                                    <div class="form-control" id="request-run-type">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-8 col-lg-9">
                        <div class="input-group">
                            <div class="input-group-addon">接口地址</div>
                            <input class="form-control" id="request-api-url" value="<?php echo LOCAL_HOST . '/' . $version_name . '/' . implode('/', array_map('strtolower', $package_list));?>">
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-3" data-toggle="tooltip" data-placement="top" title="" data-original-title="实际调用的接口版本">
                        <div class="input-group">
                            <div class="input-group-addon">版本</div>
                            <div class="form-control" id="request-api-version"></div>
                        </div>
                    </div>
                </div>

                <div class="row form-group">

                    <div class="col-md-4 col-lg-4" data-toggle="tooltip" data-placement="top" title="" data-original-title="客户端IP">
                        <div class="input-group">
                            <div class="input-group-addon">IP</div>
                            <div class="form-control" id="request-local-ip">0.0.0.0</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="input-group">
                            <div class="input-group-addon">本地时间</div>
                            <input class="form-control" id="request-local-time">
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4">
                        <div class="input-group">
                            <div class="input-group-addon">UTC</div>
                            <input class="form-control" id="request-local-timestamp">
                        </div>
                    </div>

                </div>

                <div class="row form-group">
                    <div class="col-md-4">
                        <div class="input-group" data-toggle="tooltip" data-placement="top" title="" data-original-title="本地运行总耗时（单位: s）">
                            <div class="input-group-addon">总耗时</div>
                            <div class="form-control" id="request-duration">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group" data-toggle="tooltip" data-placement="top" title="" data-original-title="请求直至获得回复的网络耗时（单位: s）">
                            <div class="input-group-addon">通信耗时</div>
                            <div class="form-control" id="request-transmission">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group" data-toggle="tooltip" data-placement="top" title="" data-original-title="服务端接口工作耗时（单位: s）">
                            <div class="input-group-addon">API耗时</div>
                            <div class="form-control" id="request-runtime">0</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inport_json">返回结果集</label>
                    <pre id="api-content">

                    </pre>
                </div>

            </div>
        </div>
    </div>
</div>

<link href="//cdn.bootcss.com/iCheck/1.0.2/skins/all.css" rel="stylesheet">
<script src="//cdn.bootcss.com/iCheck/1.0.2/icheck.min.js"></script>

<script src="<?php echo STATICS_URL;?>plugin/layer/layer.js"></script>
<script src="<?php echo STATICS_URL;?>js/jquery.sha1.js"></script>
<script src="<?php echo STATICS_URL;?>js/cookie.js"></script>

<script src="<?php echo STATICS_URL;?>js/des.js"></script>
<script src="<?php echo STATICS_URL;?>js/API-Rest.js"></script>

<script type="text/javascript">
    var c_time = 0,
        e_time = 0,
        all_time = 0;

    Date.prototype.format = function(format) {
        var date = {
            "M+": this.getMonth() + 1,
            "d+": this.getDate(),
            "h+": this.getHours(),
            "m+": this.getMinutes(),
            "s+": this.getSeconds(),
            "q+": Math.floor((this.getMonth() + 3) / 3),
            "S+": this.getMilliseconds()
        };
        if (/(y+)/i.test(format)) {
            format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
        }
        for (var k in date) {
            if (new RegExp("(" + k + ")").test(format)) {
                format = format.replace(RegExp.$1, RegExp.$1.length == 1
                    ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
            }
        }
        return format;
    };


    $(document).ready(function(){
        $('.check-params').iCheck({
            checkboxClass: 'icheckbox_flat-grey',
            radioClass: 'iradio_flat'
        });


        $("[data-toggle='tooltip']").tooltip();
    });

    function send(self)
    {
        var method = $('#api-method').text().trim(),
            host = $('#api-host option:selected').val().trim(),
            url = $('#api-url').data('url'),
            timestamp = new Date().getTime(),
            form = {};

        var isFile = $('.form-item input[type="file"]').length > 0;
        if(isFile){
            form = new FormData();
        }

        $('.form-item').each(function(){
            var isCheck = $(this).find('.is-check input').is(':checked');
            if(isCheck)
            {
                var key = $(this).find('input[name="key"]').val().trim(),
                    $val = $(this).find('input[name="value"]');

                var type = $val.attr('type');
                if(type == 'file'){
                    var params = $val[0].files[0];
                }else{
                    var params = $val.val().trim();
                }

                if(isFile){
                    form.append(key, params);
                }else{
                    form[key] = params;
                }
            }
        });

        var setting = {};
        setting.url = host + '/' + url;
        setting.data = form;
        setting.type = method;

        if(isFile) {
            setting.processData = false;
            setting.contentType = false;
        }

        setting.beforeSend = function(){c_time = new Date().getTime();};

        setting.error = function(XMLHttpRequest, textStatus, errorThrown)
        {
            var newDate = new Date();
            newDate.setTime(timestamp);

            $('#request-runtime').text(0);
            $('#request-local-time').val(newDate.format('yyyy-MM-dd h:m:s'));
            $('#request-local-timestamp').val(timestamp);
            $('#request-duration').text(0);
            $('#request-local-ip').text('0.0.0.0');
            $('#request-api-version').text('');
            $('#request-transmission').text(0);
            $('#api-content').text('');

            var $run_type = $('#request-run-type');
            $run_type.html('<span class="label label-danger" style="margin-right:10px;">通信失败</span>');

            self.removeAttr('disabled');
            self.find('span').removeClass('glyphicon-transfer').addClass('glyphicon-send');
        };

        setting.success = function(res, m, xhr)
        {
            var str = JSON.stringify(res, null, 4);
        
            var newDate = new Date();
            newDate.setTime(timestamp);

            $('#request-runtime').text(res.status.runtime);
            $('#request-local-time').val(newDate.format('yyyy-MM-dd h:m:s'));
            $('#request-local-timestamp').val(timestamp);

            e_time = new Date().getTime();
            all_time = ((e_time - c_time) / 1000).toFixed(4);
            $('#request-duration').text(all_time);

            var ttfb = all_time - res.status.runtime;
            $('#request-transmission').text(ttfb.toFixed(4));

            $('#request-local-ip').text(xhr.getResponseHeader('Remote-Address'));
            $('#request-api-version').text(xhr.getResponseHeader('Api-Version'));

            var $run_type = $('#request-run-type');
            $run_type.html('<span class="label label-success" style="margin-right:10px;">通信正常</span>');
            if(Number(res.status.code) == 0) {
                $run_type.append('<span class="label label-success" style="margin-right:10px;">框架状态: 成功</span>');
                $run_type.append('<span class="label label-success" style="margin-right:10px;">接口返回: 成功</span>');
            } else {
                if(Number(res.status.code) < 1000 ){
                    $run_type.append('<span class="label label-danger" style="margin-right:10px;">框架状态: 错误</span>');
//                            $run_type.append('<span class="label label-warning" style="margin-right:10px;">接口返回: 错误</span>');
                }else{
                    $run_type.append('<span class="label label-success" style="margin-right:10px;">框架状态: 成功</span>');
                    $run_type.append('<span class="label label-warning" style="margin-right:10px;">接口返回: 错误</span>');
                }
            }
            
            $('#api-content').html(str);
        };

        setting.complete = function(xhr, m)
        {
            $('#request-api-url').val(host + '/' + url);

            self.removeAttr('disabled');
            self.find('span').removeClass('glyphicon-transfer').addClass('glyphicon-send');
        };

        self.attr('disabled', 'disabled');
        self.find('span').removeClass('glyphicon-send').addClass('glyphicon-transfer');
        API.request(setting);
    }
</script>