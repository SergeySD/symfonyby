$(document).ready(function(){
    
});

function loadDisqus(api, callback, params)
{
    var url = 'https://disqus.com/api/3.0/'+api+'.jsonp?callback='+callback+'&api_key='+disqus_public_key+'&'+params;
    (function () {
        var s = document.createElement('script'); s.async = true;
        s.type = 'text/javascript';
        s.src = url;
        (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
    }());
}

function updateVotes()
{
    loadDisqus('threads/list', 'updateVotesData', 'forum=symfonyby');
}

function updateVotesData(data)
{
    var res = data.response;
    for (i in res)
    {
        var cnt = res[i].likes - res[i].dislikes;
        var id = res[i].identifiers[0];
        if ($('#votes'+id))
            $('#votes'+id).html(cnt);
    }
}