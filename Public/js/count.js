/**
 * 计数器JS文件
 * 用在静态化首页 index.html
 */

var newsIds = {};
$(".news_count").each(function(i){
    //获取显示的所有文章的id
    newsIds[i] = $(this).attr("news-id");
});

//调试
//console.log(newsIds);

url = "/index.php?c=index&a=getCount";

$.post(url, newsIds, function(result){
    if(result.status  == 1) {
        counts = result.data;
        $.each(counts, function(news_id,count){
            $(".node-"+news_id).html(count);
        });
    }
}, "JSON");