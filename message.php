<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <title>留言版</title>
    <link rel="stylesheet" href="/static/css/message.css" type="text/css">
    <script src="/static/js/jquery.js"></script>
    <!-- <script src="https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js"></script> -->
    <script src="/static/kindeditor/kindeditor-all.js"></script>
    <script>
        //html实体解码，实体转为字符串。
        function htmlDecodeByRegExp(str) {
            var temp = "";
            if (str.length == 0) return "";
            temp = str.replace(/&amp;/g, "&");
            temp = temp.replace(/&lt;/g, "<");
            temp = temp.replace(/&gt;/g, ">");
            temp = temp.replace(/&nbsp;/g, " ");
            temp = temp.replace(/&#39;/g, "\'");
            temp = temp.replace(/&quot;/g, "\"");
            return temp;
        }

        var page = 1 // 默认获取第一页数据
        var num = 6 // 默认每页有8行记录
        var totalPages = 0;


        function load(page,search=null) { // 加载第 page页的数据
            function showPage(search=null) {
                $.ajax({
                    async: false, // 异步 才能够让 totalPages 获得值后 再 输出到html里
                    url: "totalnums.php",
                    type: "GET",
                    data:{'search':search},
                    dataType: 'text', // 直接返回一个数字 类型简单就好
                    success: function (data) {
                        if (totalnums = parseInt(data)) {
                            console.log("totalnums:", data)
                            totalPages = Math.ceil(totalnums / num)
                        } else {
                            alert(data);
                            return;
                        }
                    }
                })
                var res = "<input type='button' id='prev_page' value='<<' /> &nbsp;&nbsp;&nbsp; "
                res += page + " / " + totalPages
                res += " &nbsp;&nbsp;&nbsp;<input type='button' id='next_page' value='>>' />"
                $("#div_page").html(res);

                $("#prev_page").click(function () {
                    if (page > 1) {
                        page--
                    }
                    load(page,search)
                })
                $("#next_page").click(function () {
                    if (page < totalPages) {
                        page++
                    }
                    load(page,search)
                })
            }
            // 获取内容
            $.ajax({
                url: "messagesShow.php",
                type: "GET",
                dataType: "json",
                data: {
                    "page": page,
                    "num": num,
                    "search":search
                },
                success: function (data) {
                    str = '';
                    data.forEach(function (value, index) {
                        // console.log(index, value)
                        str += "<div class='message'>"
                        str += (index + 1) + ":" + value.title + ":" + value.content
                        str += "</div>"
                    })
                    $("#div_messages").html(htmlDecodeByRegExp(str));
                    // $("#div_messages").get(0).innerHtml = str;
                }
            })
            // 显示 翻页
            showPage(search);
        }
        $(function () {
            load(page);

            $('#btn_submit').click(function () {
                var title = $('#title').val().trim();
                var content = editor.html().trim();
                console.log("ready to submit content")
                console.log(content)
                $.ajax({
                    type: "POST",
                    url: "insertdb.php",
                    data: {
                        'title': title,
                        'content': content
                    },
                    dataType: "text",
                    success: function (data) {
                        if (data == 1) {
                            // 加载第一页 刚提交的message 应该在第一页的第一行
                            load(1)
                        } else {
                            alert('抱歉，提交失败！')
                        }

                    }
                })
            })
            $("#button_search").click(function () {
                page = 1 // 搜索 记录  又从第一页开始
                search = $("#input_search").val().trim();
                // 执行 带 search参数的 load()
                load(page,search)
            })
        })
    </script>
</head>

<body>
    <div id="container">
        <h2>留言板</h2>
        <div id="div_write">
            <div><label for='title'>标题:</label><input type="text" name="title" id='title'></div>
            <div>
                <label for='content'>内容:</label>
                <textarea name="content" id='content' style='width:500px;height:100px;display:none;'></textarea>
            </div>
            <div>
                <input type="button" name="submit" id="btn_submit" value="提交留言">
            </div>
        </div>
        <script>
            KindEditor.ready(function (K) {
                // 另外创建了个 textarea,#content被display:none
                window.editor = K.create('#content', {
                    // afterBlur: function() {
                    //     this.sync(); // 因为我提交数据时直接用 editor.html()，无需再同步到#content上来
                    // }
                });
            })
        </script>
        <div id="div_show">
            <label for="input_search">搜索</label>
            <input type="text" id="input_search" title="请输入要搜索的内容" />
            <input type='button' id="button_search" value='确定' />

            <div id="div_messages"></div>
            <div id="div_page"></div>
        </div>

    </div>


</body>

</html>