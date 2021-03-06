<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="//cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.css" rel="stylesheet">

        <style>
            html, body {
                height: 100%;
            }
            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: '微软雅黑','Helvetica Neue',sans-serif,SimHei;
            }
            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
                padding-top: 30px;
            }
            .content {
                text-align: center;
                display: inline-block;
            }
            .title {
                font-size: 96px;
            }
            .footer-brand {
                width: 100%;
                height: 180px;
                position: relative;
                left: 0;
                bottom: 0;
            }
            .footer-brand p {
                font-family: '微软雅黑','Helvetica Neue',sans-serif,SimHei;
                font-size: 1.2em;
                line-height: 180px;
                text-align: center;
            }
            .footer-brand a {
                text-decoration: none;
                color: #7AD9FD;
            }
            .footer-brand .h-red {
                color: #F17373;
            }
            .hr { margin-top: 1em; font-size: 48px; color: gray;}
            .item { margin-top: 3em;}
            .item img { box-shadow: 0 0 1px #777;}
            .item p { width: 63%; margin-left: auto; margin-right: auto;font-weight: bold;}
        </style>
    </head>
    <body>
        <a href="https://github.com/zhengjinghua/est-image-demo" class="github-corner"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>

        <div class="container">
            <div class="content">
                <div class="title">Laravel image Demo</div>
                <div class="item">
                    <p>Origin Image</p>
                    <img src="{{ $origin_path }}"><br/>
                </div>
                <div class="hr"><i class="fa fa-hand-o-down"></i></div>
                <div class="item">
                    <p>Resize To 200 * 200 Image</p>
                    <img src="{{ $origin_resize_path }}"><br/>
                </div>
                <div class="hr"><i class="fa fa-hand-o-down"></i></div>
                <div class="item">
                    <p>Resize And Add Watermark Image</p>
                    <img src="{{ $origin_add_watermark }}"><br/>
                </div>
            </div>
            <footer class="footer-brand">
              <p>< Made With <i class="fa fa-heart h-red"></i> By <a href="http://estgroupe.com/">The EST Group</a> ></p>
            </footer>
        </div>
    </body>
</html>