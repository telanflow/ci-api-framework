<div class="row">
    <div class="col-xs-12">
        <?php $p = $this->input->get('p');?>
        <?php $c = $this->input->get('c');?>
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-bars"></i>&nbsp;
                <a href="<?php echo base_url('debug/package?v=' . $version_name);?>">
                    API文档
                </a>
            </li>
            <?php
            $str = [];
            if(!empty($p)){
                $package_list = array_filter(explode('/', $p));
            }else{
                $package_list = array();
            }
            ?>

            <?php $i=1;foreach($package_list as $k => $nav):?>
            <?php $str[] = $nav;?>
                <?php if($i == count($package_list)):?>
                    <li class="active"><?php echo $nav;?></li>
                <?php else:?>
                    <li>
                        <a href="<?php echo base_url('debug/package') . '?v=' . $version_name . '&p=' . implode('/', $str);?>">
                            <?php echo $nav;?>
                        </a>
                    </li>
                <?php endif;?>
            <?php $i++;endforeach;?>

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
            <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3">
                <div class="api-header">
                    <?php if(empty($p)):?>
                        Package List：
                    <?php else:?>
                        Api List：
                    <?php endif;?>
                </div>
                <ul class="nav-list">
                    <?php if(!empty($lists)):?>
                        <?php foreach($lists as $file):?>
                            <?php
                            if(!empty($p)){
                                $name = $p . '/' . $file['name'];
                            }else{
                                $name = $file['name'];
                            } ?>

                            <?php if($file['type'] == 1):?>
                                <li class="<?php echo ($version_name == $file['version']) ? 'version' : 'no-version';?>">
                                    <a class="item" href="<?php echo base_url('debug/package?v=' . $version_name . '&p=' . $name);?>"
                                       data-toggle="tooltip" title="<?php echo $file['desc'];?>" data-placement="top">
                                        <i class="fa fa-folder-open"></i>

                                        <?php echo $file['name'];?>
                                        <small class="text-success"><?php echo $file['version'] . (!empty($file['desc']) ? ' - ' . $file['desc'] : '');?></small>
                                    </a>
                                </li>
                            <?php else:?>
                                <li class="<?php echo ($c == $file['name']) ? 'active' : '';?> <?php echo ($version_name == $file['version']) ? 'version' : 'no-version';?>">
                                    <a href="<?php echo base_url('debug/package?v=' .$version_name. '&p=' . $p . '&c=' . $file['name']);?>"
                                       class="item" data-toggle="tooltip" data-placement="top" title="<?php echo $file['desc'];?>">
                                        <i class="fa fa-file-text-o"></i>

                                        <?php echo $file['name'];?>
                                        <small class="text-success"><?php echo $file['version'] . (!empty($file['desc']) ? ' - ' . $file['desc'] : '');?></small>
                                    </a>
                                </li>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endif;?>
                </ul>
            </div>

            <div class="col-xs-8 col-sm-8 col-md-9 col-lg-9" style="border-left: 1px solid #EEE;">
                <div class="row">
                    <?php if(empty($class_info)):?>
                        <div class="col-xs-10 col-sm-8 col-md-5 col-xs-offset-1">
                            <div class="alert alert-danger">
                                温馨提示：请从左侧的Package列表中找到你需要的接口文档
                            </div>
                        </div>
                    <?php else:?>
                        <div class="col-xs-12">
                            <blockquote>
                                <strong>API ：</strong><?php echo $class_info['title'];?>
                                <footer>
                                    <?php echo !empty($class_info['author']) ? $class_info['author'] : '';?>
                                </footer>

                                <a href="<?php echo base_url('debug/api?v='.$version_name.'&p='.$p . '&c=' . $this->input->get('c'));?>" class="btn btn-info" style="position: absolute;right:20px;top:15px;">在线调试接口</a>
                            </blockquote>

                            <div class="panel panel-info-ext">
                                <div class="panel-heading">
                                    <h3 class="panel-title">接口介绍</h3>
                                </div>
                                <div class="panel-body">
                                    <section class="api-section">
                                        <dl class="dl-horizontal">
                                            <dt>接口名</dt>
                                            <dd><?php echo ucwords($class_info['name']);?></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>版本</dt>
                                            <dd><?php echo $class_info['version'];?></dd>
                                        </dl>
                                        <dl class="dl-horizontal">
                                            <dt>Method</dt>
                                            <dd>
                                                <span class="label label-info">
                                                    <?php echo $class_info['method'];?>
                                                </span>
                                            </dd>
                                        </dl>
                                        <?php if(!empty($package_uri)):?>
                                            <dl class="dl-horizontal">
                                                <dt>请求地址</dt>
                                                <dd><?php echo LOCAL_HOST . '/' . $version_name . '/' . implode('/', array_map('strtolower', $package_uri));?></dd>
                                            </dl>
                                        <?php endif;?>
                                        <dl class="dl-horizontal">
                                            <dt>功能说明</dt>
                                            <dd>
                                                <?php echo $class_info['description'];?>
                                            </dd>
                                        </dl>

                                        <?php if(!empty($class_info['precautions'])):?>
                                        <dl class="dl-horizontal">
                                            <dt class="text-danger">注意事项</dt>
                                            <dd>
                                                <span class="text text-danger">
                                                    <?php echo $class_info['precautions'];?>
                                                </span>
                                            </dd>
                                        </dl>
                                        <?php endif;?>
                                    </section>
                                </div>
                            </div>

                            <div class="panel panel-info-ext">
                                <div class="panel-heading">
                                    <h3 class="panel-title">请求参数（Request）</h3>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-bordered ">
                                        <thead>
                                            <tr>
                                                <th style="width: 140px;">参数名</th>
                                                <th style="width: 130px;">类型</th>
                                                <th style="width: 150px;">是否必需</th>
                                                <th>描述</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if($class_info['parameter']):?>
                                            <?php foreach($class_info['parameter'] as $k => $param):?>
                                                <tr>
                                                    <th>
                                                        <code><?php echo $k;?></code>
                                                        <?php if(!empty($param[4])):?>
                                                            <span class="badge" style="background-color:#4aa2ce;font-weight: 500;"><?php echo $param[4];?></span>
                                                        <?php endif;?>
                                                    </th>
                                                    <td><?php echo !empty($param[0]) ? $param[0] : '';?></td>
                                                    <td>
                                                        <?php if(!empty($param[1])):?>
                                                            <?php if($param[1] == '必需'):?>
                                                                <span class="text-danger">
                                                                    <?php echo $param[1];?>
                                                                </span>
                                                            <?php else:?>
                                                                <span><?php echo $param[1];?></span>
                                                            <?php endif;?>
                                                        <?php endif;?>
                                                    </td>
                                                    <td>
                                                        <div><?php echo !empty($param[2]) ? $param[2] : '';?></div>

                                                        <?php if(!empty($param[3])):?>
                                                            <?php if(is_array($param[3])):?>
                                                                <p>
                                                                    <pre class="small" style="color: green;"><?php echo json_format($param[3]);?></pre>
                                                                </p>
                                                            <?php else:?>
                                                                <p>
                                                                    <span class="text-success small"><?php echo $param[3];?></span>
                                                                </p>
                                                            <?php endif;?>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                            <?php endforeach;?>
                                        <?php else:?>
                                            <tr>
                                                <td colspan="12">
                                                    <div class="text-center">暂无参数</div>
                                                </td>
                                            </tr>
                                        <?php endif;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="panel panel-info-ext">
                                <div class="panel-heading">
                                    <h3 class="panel-title">输出参数（Response）</h3>
                                </div>
                                <div class="panel-body">
                                    <pre><?php
                                        if(!empty($class_info['result'])){
                                            $result = $class_info['result'];
                                        }else{
                                            $result = new ArrayObject();
                                        }

                                        $response = array(
                                            'result' => $result,
                                            'status' => array(
                                                'code'  => 'code [string | len:5]',
                                                'msg'   => 'message [string]',
                                                'runtime' => 'api runtime(ms) [double]'
                                            )
                                        );
                                        echo json_format($response);
                                        ?></pre>
                                </div>
                            </div>

                            <div class="panel panel-info-ext">
                                <div class="panel-heading">
                                    <h3 class="panel-title">返回状态值说明（status.code）</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="col-xs-12 col-md-12 col-lg-6" style="padding-left: 0;">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                            <tr>
                                                <th style="width: 150px">状态值 (code)</th>
                                                <th>提示信息 (msg)</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($class_info['code'] as $code => $str):?>
                                                <tr>
                                                    <td><code><?php echo $code;?></code></td>
                                                    <td><?php echo $str;?></td>
                                                </tr>
                                            <?php endforeach;?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12" style="padding-left: 0;">
                                        <p>系统状态码：</p>
                                        <ul class="list-unstyled">
                                            <?php foreach($class_info['_sys_code'] as $k => $item):?>
                                                <li>
                                                    <code><?php echo $k;?></code>&nbsp;&nbsp;=>&nbsp;&nbsp;<?php echo $item;?>
                                                </li>
                                            <?php endforeach;?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12" style="padding-left: 0;">
                                <div class="panel panel-info-ext">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">接口更新维护记录</h3>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="nav-log">
                                            <?php foreach($class_info['logs'] as $time => $log):?>
                                                <li>
                                                    <span class="log-time"><?php echo $time;?> :</span>
                                                    <span class="log-desc text-warning"><?php echo $log;?></span>
                                                </li>
                                            <?php endforeach;?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>

    </div>
</div>

