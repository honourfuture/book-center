@extends('layouts.app')
@section('content')
    @foreach($artisans as $artisan)
        <div class="mockup-code">
            <pre data-prefix="${{ $artisan['count'] }}"><code>{{ $artisan['artisan'] }}</code></pre>
        </div>
        <div class="mb-4"></div>
    @endforeach

    <div class="mb-4"></div>
    <div class="mockup-code">
        <pre data-prefix="$"><code>service:(nginx_log_source OR nginx_log_source_tianz) @http.referer:(*sm.cn* OR *m.baidu.com* OR *sogou.com*)</code></pre>

        <pre data-prefix="$"><code>service:nginx_log_source @http.useragent_details.device.brand:Spider </code></pre>
    </div>
    <div class="mb-4"></div>

    <div class="mb-4"></div>
    <div class="mockup-code">
        <pre data-prefix="$"><code>php74 artisan fix:chapter --site=mayi --article_id= </code></pre>
        <pre data-prefix="$"><code>php74 artisan fix:chapter --site=tt --article_id= </code></pre>
        <pre data-prefix="$"><code>php74 artisan fix:chapter --site=xwbiquge --article_id= </code></pre>
        <pre data-prefix="$"><code>php74 artisan fix:chapter --site=69shu --article_id= </code></pre>
        <pre data-prefix="$"><code>php74 artisan fix:chapter --site=00shu --article_id= </code></pre>

        <pre data-prefix="$"><code>php74 artisan source:update --type=append</code></pre>

    </div>
    <div class="mb-4"></div>

    <div class="mockup-code">
        <pre data-prefix="$"><code>function scrollDown() {
                      var currentPosition = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
                      var targetPosition = document.body.scrollHeight - window.innerHeight;

                      if (currentPosition < targetPosition) {
                        window.scrollTo(0, currentPosition + 10); // 每次滚动增加 10px 的距离
                        setTimeout(scrollDown, 100); // 间隔 10ms 执行下一次滚动
                      }
                    }
                scrollDown(); </code></pre>
    </div>
    <div class="mb-4"></div>

    <div class="mockup-code">
        <pre data-prefix="$">
            <code>
                git clone https://github.com/nvm-sh/nvm.git
                 ./install.sh

                sudo yum update
                sudo yum install -y wget curl unzip fontconfig libX11-devel libXcomposite-devel libXcursor-devel libXdamage-devel libXext-devel libXi-devel libXtst-devel libXrandr-devel libXrender-devel libXss-devel libXScrnSaver-devel libappindicator-gtk3 libappindicator-devel
                yum install pango.x86_64 libXcomposite.x86_64 libXcursor.x86_64 libXdamage.x86_64 libXext.x86_64 libXi.x86_64 libXtst.x86_64 cups-libs.x86_64 libXScrnSaver.x86_64 libXrandr.x86_64 GConf2.x86_64 alsa-lib.x86_64 atk.x86_64 gtk3.x86_64 nss.x86_64 -y
                yum install


                npm install pm2@latest -g
                pm2 start page.js
                pm2 monit

                 npm config set puppeteer_download_host=https://npm.taobao.org/mirrors
                 npm config set registry https://registry.npm.taobao.org

                {
                  "name": "remote-origin",
                  "version": "1.0.0",
                  "description": "",
                  "main": "index.js",
                  "scripts": {
                    "test": "echo \"Error: no test specified\" && exit 1"
                  },
                  "keywords": [],
                  "author": "",
                  "license": "ISC",
                  "dependencies": {
                    "express": "^4.18.2",
                    "iconv-lite": "^0.6.3",
                    "puppeteer": "^10.4.0"
                  }
                }

                cd remote-origin
                npm install express puppeteer
                npm i puppeteer-core
                node page.js
            </code>
        </pre>
    </div>
    <div class="mb-4"></div>
@endsection
