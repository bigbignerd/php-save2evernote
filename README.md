## php-save2evernote
在命令行快速将简单的想法记录到印象笔记中

####想法来源
一直使用印象笔记来整理、记录、收集知识想法，但随着笔记数目的增加，打开Evernote->找到笔记本->找到对应类型的笔记需要花费很多时间，所以想到可以通过命令行一条命令快速保存脑袋里冒出来的想法，所以基于Evernote的php sdk 做了plain text的简单实现。

####安装使用

代码克隆到本地，申请[deverlop token](https://dev.evernote.com/doc/articles/dev_tokens.php)。

根目录添加config.php文件
```
$config = array(
    'token' => 'Your token here',
    'tag' => ['mind'],
    'notebook' => 'mind',
    'note_prefix' => 'Bignerd record',//笔记标题前缀
);
```

token添加到config.php中的'your token here',然后在项目根目录下执行`php index.php 添加的笔记内容` 即可添加。

当前笔记会保存在名为Bignerd record 日期-第x周为标题的一条笔记中（笔记前缀可以在config.php中修改），我的想法是一个月按照四周划分，保存一周的想法，一周整理一次，可以自己修改规则，需修改代码。

>注：执行的php命令路径过长，可以设置alias,如果是mac用户，并且安装了alfred，并且购买了power pack,那便可以直接option+space 直接执行shell脚本保存笔记啦！
