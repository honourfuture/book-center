<div id="newscontent">

    <div class="l">
        <h2>最近更新小说列表</h2>
        <ul>
            @foreach($update_articles as $update_article)
            <li><span class="s1"></span><span class="s2"><a href="{{$update_article['article_url']}}" target="_blank">{{$update_article['article_name']}}</a></span><span class="s3"><a href="/103_103013/48082515.html" target="_blank">{{ $update_article['last_update'] }}</a></span><span class="s4">{{ $update_article['author'] }}</span><span class="s5">10-29</span></li>
            @endforeach
        </ul>
    </div>



    <div class="r">
        <h2>最新入库小说</h2>
        <ul>
{{--            <li><span class="s1">[都市]</span><span class="s2"><a href="https://www.mayiwxw.com/116_116830/index.html">都市强龙：从退婚开始</a></span><span class="s5">大口恰肉</span></li>--}}
        </ul>

    </div><div class="clear"></div>

</div>
