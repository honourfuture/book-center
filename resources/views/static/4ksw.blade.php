<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="gbk">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <title>�Ŀ�����</title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta http-equiv="Cache-Control" content="no-transform " />
    <link href="http://www.4ksw.com/web/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://www.4ksw.com/web/css/style.css" rel="stylesheet">
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">�Ŀ�����</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">�Ŀ�����</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">

            </ul>
            <form class="navbar-form navbar-right hidden-sm hidden-xs" name="articlesearch" action='/modules/article/search.php' method='post'>
                <input type="text" class="form-control form-search" placeholder="�����������ؼ���" name="searchkey">
                <input type="submit" class="form-control btn-search" value="����">
            </form>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<div class="container">
    <div id="content">
        <ol class="breadcrumb">
            <li><a href="/">��ҳ</a></li>
            <li >�����а�</li>
            <li class="active">С˵�б�</li>
        </ol>
        <div id="_17mb_ph" class="_17mb_ph">
            <ul class="caption">
                <li class="articlename">&nbsp;&nbsp;��������</li>
                <li class="lastchapter">�����½�</li>
                <li class="author">����</li>
                <li class="lastupdate">����ʱ��</li>
                <li class="visit">�����</li>
                <li class="fullflag">д��״̬</li>
                <div style="clear:both"></div>
            </ul>
            <ul class="article">
                @foreach($source_articles as $source_article)
                    <li class="articlename">&nbsp;&nbsp;<a href="{{$source_article->origin_url}}"><?php echo  iconv('utf-8', 'gbk//IGNORE', $source_article->article_name);?></a></li>
                    <li class="lastchapter"><a href="/0/12065/25836425.html" target="_blank"></a></li>
                    <li class="author"><a href="/author/%CC%FA%C2%ED%B7%C9%C7%C5" target="_blank"><?php echo  iconv('utf-8', 'gbk//IGNORE', $source_article->author);?></a></li>
                    <li class="lastupdate"></li>
                    <li class="visit"></li>
                    <li class="fullflag"></li>
                @endforeach

                <div class='clearfix'></div>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
