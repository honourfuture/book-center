# book-center 为杰奇小说站提供的一个服务应用

#- 拉取百度统计中访问量点击量最高的页面URL. 并生成HTML提供给采集器
#- 统计NGINX日志 获取访问量点击量最高的页面URL, 并生成HTML提供给采集器

- 通过php获取 "正在手打中"/"灵魂契约" 等错误章节
- 自动修复章节 
    # 蚂蚁文学
    php74 artisan fix:chapter --site=mayi --article_id=508
    # TT书吧
    php74 artisan fix:chapter --site=tt --article_id=8957
    # 69书
    php74 artisan fix:chapter --site=69shu --article_id=14

- 获取错误章节 并分页展示
- ID对比工具

- nginx 日志分析
  php74 artisan parse:nginx-log
  将logs/nginx_log中的文件输入到mysql

    cat /www/wwwlogs/www.tieshuw.com.log | grep Baiduspider > tieshu.baidu.2023_08_09.log
    cat /www/wwwlogs/www.tieshuw.com.log | grep YisouSpider > tieshu.yisou.2023_08_09.log
    cat /www/wwwlogs/www.tieshuw.com.log | grep SogouSpider > tieshu.sogou.2023_08_09.log
    mv /www/wwwlogs/tieshu.* /www/wwwroot/help-tieshuw-com/storage/logs/nginx_log/

 ``
    #!/bin/bash
    
    # 获取昨天和今天的日期
    yesterday=$(date -d "yesterday" "+%d/%b/%Y")
    today=$(date "+%d/%b/%Y")
    
    # 指定日志文件路径
    access_log="/www/wwwlogs/www.tieshuw.com.log"
    
    # 指定保存日志的目录
    log_directory="/www/wwwroot/help-tieshuw-com/storage/logs/nginx_log/"
    
    # 指定提取日志的关键词（多个关键词用逗号分隔）
    keywords=("Baiduspider" "360Spider" "SogouSpider" "Sogou web spider" "YisouSpider")
    
    # 将数组转换为逗号分隔的字符串
    regex=$(IFS="|"; echo "${keywords[*]}")
    
    # 提取昨天一整天的日志并保存到文件
    grep "$yesterday" $access_log | grep -E "$regex" > "${log_directory}tieshuw_spider_$(date -d "yesterday" "+%Y_%m_%d").log"
    
    echo "已提取并保存昨天一整天的Nginx访问日志中包含关键词的数据到文件 ${log_directory}tieshuw_spider_$(date -d "yesterday" "+%Y_%m_%d").log"
``   
