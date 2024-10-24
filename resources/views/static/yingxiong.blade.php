<div class="update-wrap">
    <h3 class="wrap-title">最近更新</h3>
    <div class="update-table">
        <table>
            <colgroup>
                <col width="80px">
                <col width="200x">
                <col width="270px">
                <col width="120px">
                <col width="60px">
            </colgroup>
            <tbody>
            @foreach($update_articles as $update_article)
            <tr>
                <td><span class="classify">「女生小说」</span></td>
                <td><a class="name" href="{{$update_article['article_url']}}">{{$update_article['article_name']}}</a></td>
                <td><a class="section" href="{{$update_article['article_url']}}">{{ $update_article['last_chapter'] }}</a></td>
                <td><span class="author">{{$update_article['author']}}</span></td>
                <td><span class="time">10-24</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
