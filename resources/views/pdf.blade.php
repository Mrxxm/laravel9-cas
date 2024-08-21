<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>报告</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        .preview-area {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 1px;
            margin: 0 auto;
            width: 1105px;
            height: auto;
            overflow-y: scroll;
            transform: translate(-50%, 0);
            -webkit-transform: translate(-50%, 0);
            -moz-transform: translate(-50%, 0);
        }

        .preview-area::-webkit-scrollbar {
            width: 0;
        }

        #preview {
            padding: 40px;
        }

        .preview-box {
            background: #ffffff;
            padding: 22px 0 27px;
        }


        * {
            margin: 0;
            padding: 0;
            word-wrap: break-word;
            word-break: break-all;
        }

        ul li {
            list-style: none;
        }

        html,
        body,
        #app,
        .wrapper {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'PingFang SC', 'Helvetica Neue', Helvetica, 'microsoft yahei', arial, STHeiTi, sans-serif;
        }

        a {
            text-decoration: none;
        }

        .clearFix:after {
            content: ' ';
            display: table;
            clear: both;
        }

        .text_cen {
            text-align: center;
        }

        .template-table {
            width: 100%;
            border-spacing: 0;
            border: 1px solid #dddddd;
            /*border-bottom: 0;隐藏下侧边框*/
            /*border-right: 0;隐藏右侧边框*/
        }

        .template-table td {
            font-size: 12px;
            color: #606266;
            padding: 10px;
            border-bottom: 1px solid #dddddd;
            border-right: 1px solid #dddddd;
        }

        .center td {
            text-align: center;
        }

        .template-table_title {
            background: #f4fcfb;
            padding: 10px;
            border-right: 1px solid #dddddd;
        }

        .template-table_title.group {
            background: #fafafa;
        }

    </style>
</head>
<body>


<div id="app">
    <div class="preview-area">
        <div>
            <div class="preview-box" id="watermark">
                <div class="text_cen">
                    <p style="font-size: 14px;">{{ $title }}</p>
                </div>

                @if(isset($type) && $type == 'cso_report_audit')
                    <div style="justify-content: space-between; float: left; width: 50%">
                        编号:{{ $report_sn }}
                    </div>
                    <div style="justify-content: space-between; float: right; width: 50%; text-align: right">
                        提交时间:{{ $submit_at }}
                    </div>
                @endif
                <table class="template-table">
                    <tr>
                        <th style="width: 10%"></th>
                        <th style="width: 40%"></th>
                        <th style="width: 10%"></th>
                        <th style="width: 40%"></th>
                    </tr>

                    <tr>
                        <td class="template-table_title">T名称:</td>
                        <td colspan="3">{{ $t_name }}</td>
                    </tr>
                    <tr>
                        <td class="template-table_title">C名称:</td>
                        <td colspan="3">{{ $c_name }}</td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
