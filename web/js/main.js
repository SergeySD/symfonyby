$(document).ready(function(){
    var url = 'https://disqus.com/api/3.0/threads/list.jsonp?forum=symfonyby&callback=updateVotes&api_key='+disqus_public_key;
    (function () {
        var s = document.createElement('script'); s.async = true;
        s.type = 'text/javascript';
        s.src = url;
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
});

function updateVotes(data)
{
    var res = data.response;
    for (i in res)
    {
        var cnt = res[i].likes - res[i].dislikes;
        var id = res[i].identifiers[0];
        console.log(cnt)
        console.log(id)
        if ($('#likes'+id))
            $('#likes'+id).html(cnt);
    }
}