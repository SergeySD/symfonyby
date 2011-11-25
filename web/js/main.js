function disqusVote(vote, id)
{
    var url = 'https://disqus.com/api/3.0/threads/vote.json?api_key='+disqus_public_key;
    $.post(url, {vote: vote, "thread:ident": id, forum: "symfonyby"}, function (res)
    {
        
    }, "json");
}