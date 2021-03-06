# php 小demo之 留言板
用到了 juqery ajax mysql kindeditor
数据库图简单就设了三个字段 id(int paimary),title(varchar(255)),content(text)

## kindeditor 富文本编辑器插件
[官方文档](http://kindeditor.net/doc.php)
核心使用 textarea输入框 + 创建editor对象(原来的textarea被display:none)
```html
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id');
        });
        // 获取数据
        editor.html()
</script>
```



## 翻页
<<  page/Math.ceil(totalnums/num) >> 两边两个button
结合  sql语句**limit**
```php
sprintf("select id,title,content from message order by id desc limit %d,%d ",($page-1)*$num,$num);
```

## 分页加载留言的流程
1. 到messagesShow.php  GET 某一页的数据 参数：页码page,每页行数num
2. 显示翻页器 showPage
3. 增加 关键字搜索功能(对应sql like)，添加参数search 将之前的加载函数重写？重载

## 重点：
查询表中总记录数， mysql中schema指的就是数据库 
```php
 //select table_rows FROM tables where table_name='message' and table_schema='test'
    if ($res = $mysqli->query("select table_rows FROM information_schema.tables where table_name='message' and table_schema='test'")) { // test.message
        echo $res->fetch_all()[0][0]; // 第一条记录的第一个字段
    } 
```
模糊查询的总记录数 select count() 
```php
    $sql = sprintf("select count(id) from message where title like '%%%s%%' or content like '%%%s%%' order by id desc",$search,$search);
```

## 坑之 注意程序运行的顺序，有时不得不（但是尽量不）  ajax设置为同步 async: false,
如翻页功能，可以等后台返回 总记录条数， 才能在后边html里显示 总记录条数，因此前边ajax获取需要
```php
 $.ajax({
                    async: false, 
                    url: "totalnums.php",
                    ... })
```
也可以 将依赖 ajax返回的代码写到success里，这个返回有点时间，可以模仿文件操作，加锁，如：
```javascript
// 防止total 未更新就点击按钮 导致bug  
                        $("#prev").attr({ disabled: false })
                        $("#next").attr({ disabled: false })
```

## 坑之html实体
浏览器渲染后 出现 &nbsp;&amp; &lt;&gt; 等实体字符，这是从后台拿来的raw数据没解码
```javascript
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
 //html编码，字符串转为实体
        function htmlEncodeByRegExp(str) {
            var temp = "";
            if (str.length == 0) return "";
            temp = str.replace(/&/g, "&amp;");
            temp = temp.replace(/</g, "&lt;");
            temp = temp.replace(/>/g, "&gt;");
            temp = temp.replace(/\s/g, "&nbsp;");
            temp = temp.replace(/\'/g, "&#39;");
            temp = temp.replace(/\"/g, "&quot;");
            return temp;
        }
```

## 坑之浏览器缓存
在开发中，有些东西明明修改了，但是浏览器显示仍未修改，很有可能是浏览器的缓存在作怪，特别是**static/**文件夹中的静态文件
f12网络条件中可以设置<span style='color:red'>禁用缓存</span>,或者直接打开private隐私窗口
