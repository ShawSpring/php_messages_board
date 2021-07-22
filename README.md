# php 小demo之 留言板
用到了 juqery ajax mysql kindeditor
数据库图简单就设了三个字段 id(int paimary),title(varchar(255)),content(text)

## kindeditor 副文本编辑器插件
[官方文档](http://kindeditor.net/doc.php)
核心使用 textarea输入框 + 创建editor对象(原来的textarea被display:none)
```html
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id');
        });
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

## 坑之 注意程序运行的顺序，有时不得不  ajax设置为同步 async: false,
如翻页功能，必须要等后台返回 总记录条数， 才能在后边html里显示 总记录条数，因此前边ajax获取需要
```php
 $.ajax({
                    async: false, 
                    url: "totalnums.php",
                    ... })
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
```
