<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API调试</title>

    <!-- font-awesome -->
    <link href="//cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="<?php echo STATICS_URL;?>plugin/bootstrap-3.3.0/css/bootstrap.min.css" rel="stylesheet">

    <link href="//cdn.bootcss.com/animate.css/3.5.2/animate.min.css" rel="stylesheet">

    <link href="<?php echo STATICS_URL;?>css/bt_ext.css" rel="stylesheet">
    <link href="<?php echo STATICS_URL;?>css/api.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo STATICS_URL;?>js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo STATICS_URL;?>plugin/bootstrap-3.3.0/js/bootstrap.min.js"></script>

    <script src="<?php echo STATICS_URL;?>js/jquery.md5.js"></script>
    <script src="<?php echo STATICS_URL;?>js/api-base.js"></script>
</head>
<body>
    <header class="header-bar">
        <h2>
            <a href="<?php echo base_url('debug/package' . (!empty($version_name) ? '?v=' . $version_name : ''));?>" style="text-decoration: none; color:#FFF;">API DOCUMENT</a>
            <small class="text-warning"> - telan</small>

            <div class="btn-list">
                <ul>
                    <li>
                        <a href="<?php echo base_url('debug/specification' . (!empty($version_name) ? '?v=' . $version_name : ''));?>"
                           data-toggle="tooltip" title="开发配置、规范" data-placement="right">
                            <i class="glyphicon glyphicon-wrench"></i>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo base_url('debug/help' . (!empty($version_name) ? '?v=' . $version_name : ''));?>"
                           data-toggle="tooltip" title="帮助文档" data-placement="right">
                            <i class="glyphicon glyphicon-book"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </h2>
    </header>

    <div class="container-fluid">