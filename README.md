# book-center 为杰奇小说站提供的一个服务应用

#- 拉取百度统计中访问量点击量最高的页面URL. 并生成HTML提供给采集器
#- 统计NGINX日志 获取访问量点击量最高的页面URL, 并生成HTML提供给采集器

- 通过php获取 "正在手打中"/"灵魂契约" 等错误章节
- 自动修复章节 
    # 蚂蚁文学
    php74 artisan fix:chapter --site=mayi --article_id=508
    # TT书吧
    php74 artisan fix:chapter --site=tt --article_id=30259
    # 69书
    php74 artisan fix:chapter --site=69shu --article_id=14

- 获取错误章节 并分页展示
- ID对比工具
