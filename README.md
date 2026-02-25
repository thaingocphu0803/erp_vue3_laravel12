# Deploy Workflow

### I. Dockerfile

#### 1. Nginx

##### # Giai đoạn builder:

- Sử dụng baseImage là **node:20-alpine**

- Đặt thư mục làm việc ở giai đoạn này thành **/app** -> Copy file **frontend/package\*/json** từ máy host vào thư mục **/app** trong container

- Cài đặt dependencies và đặt trong nó trong thư mục **/root/.npm** ở lần đầu, những lần sau sử dụng dependencies trong thư mục này.

- Copy thư mục **frontend** từ máy host vào thư mục **/app** trong container -> Thực thi lệnh **npm run build** để build dự án vào thư mục **dist**

##### # Giai đoạn production:

- Copy file **/docker/nginx/nginx.conf** từ máy host vào **/etc/nginx/templates/default.conf.template** của container với **nginx.conf** có cấu hình server như sau:
    - Lắng nghe cổng **80**
    - **server_name** nhận từ **compose.yml**
    - Mỗi request mặc định đi qua **index.php** và **index.html**
    - Tất cả url đều đi qua thư mục root là **/var/www/public/dist** -> kiểm tra xem có tồn tại **file** nào khớp với url không-> Kiểm tra xem có tồn tại **folder** nào khớp url không -> Nếu tất cả đều không khớp thì đưa đến **index.html** để xử lý.

    - Tất cả url bắt đầu với **/api** điều kiểm tra **file**, **folder**. Nếu tất cả đều không khớp thì đưa đến **index.php** để xử lý kèm với **\$query_string** parameters -> Url **/api/token** sẽ được chuyển thành **sanctum/csrf-cookie**

    - Tất cả url kết thúc với **\*.php** thì **fastgci_pass** sẽ chuyển sang **php-fpm server** để xử lý -> Chỉ định **fastcgi_index** là **index.php** để xử lý -> Tạo biến **SCRIPT_FILENAME** với lệnh **fastcgi_param** để chỉ đường cho php-fpm đi tới file code. Cấu trúc **SCRIPT_FILENAME** được cấu hình từ **\$document_root** và **\$fastcgi_script_name** -> Gửi dữ liệu trong **fastcgi_params** tới php-fpm với lệnh **include**

- Đặt thư mục làm việc ở giai đoạn này là **/var/www**

- Copy toàn bộ folder **/backend** từ máy host vào thư mục **/var/www** của container -> Copy toàn bộ folder **/app/dist** từ giai đoạn build sang thư mục **/var/www/public/dist** của giai đoạn production

- Thay đổi owner:group của thư mục **/var/www** thành **nginx:nginx**

- Mở cổng **80** và khởi động **Nginx Server** trên cổng này.

---

#### 2. php-fpm

##### # Giai đoạn builder:

- Sử dụng **baseImage** là **php:8.4-fpm**

- Cài đặt dependencies -> Xóa kho lưu tữ tạm thời của **apt-get** -> Xóa các gói phần mềm từ lệnh **apt-get update** nằm trong thư mục **/var/lib/apt/lists/\*** và các tệp tin tạm thời được tạo ra bởi các ứng dụng đang chạy nằm trong thư mục **/tmp/\*** và **/var/tmp/\***

- Đặt thư mục làm việc cho container ở giai đoạn này là **/var/www** -> Copy thư mục **backend** từ máy host vào **/var/www/** của container

- Tải composer vào thư mục **root/.composer/cache** trong lần build đầu tiên, các lần build sao sẽ sử dụng compose từ thư mục này -> cài đặt

##### # Giai đoạn production:

- Cài đặt dependencies cho giai đoạn production và cho health check -> Xóa kho lưu tữ tạm thời và các dependencies không còn dùng của **apt-get** -> Xóa các gói phần mềm từ lệnh **apt-get update** nằm trong thư mục **/var/lib/apt/lists/\*** và các tệp tin tạm thời được tạo ra bởi các ứng dụng đang chạy nằm trong thư mục **/tmp/\*** và **/var/tmp/\***

- Tải **php-fpm-healthcheck** về thư mục **/usr/local/bin/php-fpm-healthcheck** -> Cấp quyền thực thi cho thư mục **/usr/local/bin/php-fpm-healthcheck**

- Copy tệp **entrypoint.sh** từ máy host vào tệp **/usr/local/bin/entrypoint.sh** của container -> Cấp quyền thực thi cho tệp **/usr/local/bin/entrypoint.sh**

- Copy thư mục **backend/storage** từ máy host vào **/var//www/storage-init** của container

- Copy các thư mục **usr/local/lib/php/extensions**, **usr/local/etc/php/config.d**, **usr/local/bin/docker-php-ext-\*** từ giai đoạn builder vào các thư mục **usr/local/lib/php/extensions**, **usr/local/etc/php/config.d**, **usr/local/bin/** của giai đoạn production

- Đổi tên file **php.init-production** thành **php.ini**

- Thêm nội dung vào file **/usr/local/etc/php-fpm.d/my-fpm.conf** để phục vụ cho **php-fpm-healthcheck**

- Copy **/var/www** từ giai đoạn builder vào **/var/www** của giai đoạn production -> Đặt thư mục làm việc ở giai đoạn này là **/var/www** -> Thay đổi owner:group của thư mục thành **www-data** -> Chỉ định người thực hiện các tiến trình tiếp theo thành **www-data**

- Thực thi file **entrypoint.sh** gồm các bước:
    - Kiểm tra nếu thư mục **/var/wwww/storage** chưa tồn tại thì copy nội dung của thư mục **/var/www/storage-init** ở giai đoạn production vào **/var/www/storage** -> Thay đổi owner:group cho thư mục **/var/www/storage** thành **www-data:www-data**
    - Xóa thư mục **/var/www/storage-init**

    - Thực thi lệnh **php artisan migrate --force --seed** để tạo bảng -> Thực thi lệnh **php artisan config:cache** để cache cofig -> Thực thi lệnh **php artisan route:cache** để cache route

    - Tiếp tục thực thi các lệnh tiếp theo trong giai đoạn production

- Mở cổng **9000** -> Thực thi lệnh **php-fpm** để chạy php-fpm server

---

#### 3. php-cli

##### # Giai đoạn builder:

Tương tự php-fpm dockerfile

##### # Giai đoạn production:

Tương tự php-fpm dockerfile nhưng không thực thi **entrypoint.sh**

### II. Docker Compose

> Network dùng chung cho tất cả container là **app-production** với **driver: brige**

#### 1. Ngix

- Image được push và pull từ **GAR**

- Buil với dockerfile **/docker/php-fpm/Dockerfile** ở giai đoạn production

- Volume **laravel-storage-production** gắn với thư mục **/var/www/storage** của container cùng quyền chỉ đọc

- Mapping port **80** của container với port của host

- Khai báo biến eviroment là **NGINX_HOST** để sử dụng trong container

- Đợi healtcheck cho **php-fpm container** trước khi khởi động container

- Luôn khởi động lại container nếu không được tắt thủ công

---

#### 2. php-fpm

- Image được push và pull từ **GAR**

- Buil với dockerfile **/docker/php-fpm/Dockerfile** ở giai đoạn production

- Chỉ định file .env được sử dụng trong container sau khi build xong

- Volume **laravel-storage-production** gắn với thư mục **/var/www/storage** của container cùng quyền chỉ đọc

- Healthcheck với lệnh **php-fpm-healthcheck**

- Đợi healtcheck cho **mysql container** trước khi khởi động container

- Luôn khởi động lại container nếu không được tắt thủ công

---

#### 3. php-cli

- Image được push và pull từ **GAR**

- Buil với dockerfile **/docker/php-cli/Dockerfile** ở giai đoạn production

- Chỉ định file .env được sử dụng trong container sau khi build xong

- Cho phép tương tác với terminal

- Giữ cho luồn nhập liệu luôn mở

---

#### 4. mysql

- Sử dụng baseImage là **mysql:8.0.44**

- Thay đổi user thực thi lệnh bên trong container thành **mysql**

- Volume **mysql-db-production** gắn với thư mục **/var/lib/mysql** của container

- Mapping port **3306** của container với port của host

- Khai báo biến eviroment là **MYSQL_ROOT_PASSWORD**, **MYSQL_DATABASE**, **MYSQL_USER**, **MYSQL_PASSWORD** để sử dụng trong container

- Healthcheck với lệnh **mysqladmin ping -h localhost -u root -p\$\$MYSQL_ROOT_PASSWORD**

- Luôn khởi động lại container nếu không được tắt thủ công

---

#### 5. cloudflare-tunnel

- Sử dụng baseImage là **cloudflare/cloudflared:latest**

- Khai báo biến eviroment là **TUNNEL_TOKEN** để sử dụng trong container

- command **tunnel --no-autoupdate run** được thực thi để kết nối container với **Cloudflare**

- Healthcheck với lệnh **cloudflared --version**

- Đợi **nginx container** được khởi động trước khi khởi động container

- Luôn khởi động lại container nếu không được tắt thủ công

### III. Github Actions

#### 1. CI

> Thực thi khi tạo **pull request** vào nhánh **main**

> Tất cả Runner chạy trên Ubuntu 22.04 

##### # Laravel test job

- Chỉ định thư mục **backend** cho tất cả các bước trong job

- Sử dụng action **actions/checkout@v4** để checkout repository và chỉ định thư mục checkout là **backend**

- Sử dụng action **shivammathur/setup-php@v2** để xây dựng môi trường PHP ở phiên bản **8.4**

- Thực thi lệnh **php -r "file_exists('.env') || copy('.env.example', '.env')"** để kiểm tra xem đã tồn tại file **.env** chưa. Nếu chưa thì copy từ file **.env.example** và đổi tên file thành **.env**

- Cài đặt **composer dependencies**

- Khởi tạo app key với lệnh **php artisan key:generate** -> Chỉ định quyền đọc -  ghi - thực thi cho thư mục **storage** và **boostrap/cache** để đảm bao ứng dụng không phát sinh lỗi trong quá trình testing

- Thực thi lệnh **php artisan test --testsuite=Feature** để thực hiện **Feature Test**

---

##### # Vuetify test job

- Chỉ định thư mục **frontend** cho tất cả các bước trong job

- Sử dụng action **actions/checkout@v4** để checkout repository và chỉ định thư mục checkout là **frontend**

- Sử dụng action **actions//setup-node@v4** Xây dựng môi trường node với phiên bản **22.\***. Ngoài ra thuộc tính **cache** cũng được sử dụng để **cache npm dependencies** và thuộc tính **cache-dependency-path** đê theo dõi sự thay đổi của file **frontend/package-lock.json**

- Cài đặt **npm dependencies**

- Thực thi lệnh **npm run test:unit** để thực hiện **Unit Test**

---

##### # Docker build & push job

- Chờ job laravel test và vuetify test hoàn tất thì job này mới bắt đầu chạy

- Khai bao môi trường là **develop** để sử dụng các biến **Github secret** và 
**Github variable** của môi trường này

- Khai báo biến môi trường **GCP_WIP** và **GCP_SERVICE_ACCOUNT** để dùng cho action **google-github-actions/auth@v3**

- Chỉ định quyền **content: read** để cho phép Action checkout repository và **id-token: write** để cho phép Github tạo **OIDC token** để xác thực với **GCP**

- Sử dụng action **actions/checkout@v4** để checkout repository

- Sử dụng action **google-github-actions/auth@v3** để xác thực giữa **Github Actions** và **GCP** với các thuộc tính sau:
	- **workload_identity_provider: \${{env.GCP_WIP}}**: đường dẫn xác thực đầy đủ của Workload Identity Provider trên **GCP**
	
	- **service_account: \${{env.GCP_SERVICE_ACCOUNT}}**: email service account trên **GCP** sử dụng cho GitHub Action
	
	- **token_format: access_token**: token format sử dụng cho OAth2

- Sử dụng action **docker/login-action@v3** để login vào **GAR** với các thuộc tính sau:
	- **registry: \<region\>-docker.pkg.dev**: Chỉ định khu vực **GAR** đang được đặt.

	- **username: oauth2accesstoken**: Măc định xác thực với **GCP** bằng OAth2 access token

	- **password: \${{steps.auth.outputs.access_token}}**: access token mặc định được sinh ra sau khi xác thực với **GCP** ở bước trước đó

- Khai báo các biến env từ **Github secret** và **Github variable** để sử dụng cho các file **.env** trong repository -> Thực thi lệnh **envsubst \< \.env\.example \> \.env** và **envsubst \< backend\/\.env.example \> backend\/\.env** để thay thế các tham số **$\*** thành biến ở step hiện tại sau đó xuất kết quả vào file **\.env** ở môi trường Runner

- Sử dụng action **docker/setup-buildx-action@v3** để xây dựng trình biên dịch **Buildx** để **build docker Images** trên đa nền tảng và hỗ trợ caching

- Sử dụng action **docker/bake-action@v6** với thuộc tính **with.call: check** để kiểm tra cấu hình docker trước khi build thật


- Sử dụng action **docker/bake-action@v6** với thuộc tính **with.push: true** để build images và đẩy images lên kho lưu trữ **GAR** sau khi build xong. Ngoài ra thuộc tính **with.set: \*.cache-from=type=gha \&& \*.cache-to=type=gha,mode=max** giúp lưu và sử dụng cache trong **Github Actions**

---

#### 2. CD

> Thực thi khi thực hiện **push/merge** vào nhánh **main**

> Tất cả Runners chạy trên Ubuntu 22.04

> **--tunnel-through-iap** option cho phép ssh vào **GCE** từ môi trường runner để thực hiện thao tác với VM

##### # Deloy lên GCE

- Khai báo biến môi trường dùng chung cho toàn bộ step trong job này

- Chỉ định quyền **content: read** để cho phép Action checkout repository và **id-token: write** để cho phép Github tạo **OIDC token** để xác thực với **GCP**

- Sử dụng action **actions/checkout@v4** để checkout repository

- Sử dụng action **google-github-actions/auth@v3** để xác thực giữa **Github Actions** và **GCP** tương tự như trong job **docker build && push**

- Khai báo các biến **env** và tạo file **.env** với lệnh **envsubst** tương tự như trong job **docker build && push**

- Thực thi lệnh **mkdir -p \/home\/runner\/.ssh** để tạo thư mục chứa ssh key trong môi trường runner -> Thực thi lệnh **ssh-keygen -t rsa -f \/home\/runner\/.ssh\/google_compute_engine -N "" -C "github-actions"** để tạo ssh key và lưu key trong thư mục vừa tạo

- Sử dụng action **google-github-actions/setup-gcloud@v3** để cài đặt gcloud cli trên môi trường runner

- Thực thi lệnh **gcloud compute os-login ssh-keys add ...** để thêm ssh key tạo ở runner vào OS login của **GCP**


- Thực thi lệnh **gcloud compute ssh --command="mkdir -p \~\/deploy\/backend" --tunnel-through-iap** để tạo thư mục **deploy/backend** trên **GCE**.

- Thực thi lệnh **gcloud compute scp .env compose.prod.yml ${{env.GCP_VM_NAME}}\:\~\/deploy --tunnel-through-iap** để sao chép file **.env** **compose.prod.yml** từ môi trường runner vào thưc mục **deploy** trên **GCE**.

- Thực thi lệnh **gcloud compute scp backend/.env ${{env.GCP_VM_NAME}}\:\~\/deploy\/backend --tunnel-through-iap** để sao chép file **.env** **compose.prod.yml** từ môi trường runner vào thưc mục **deploy\/backend** trên **GCE**.

- Thực thi lệnh **gcloud compute ssh --command="mkdir -p \~\/deploy\/backend" --tunnel-through-iap** để tạo thư mục **deploy/backend** trên **GCE**.

- Thực thi lệnh **gcloud compute ssh --command ...** để gộp nhiều thao tác lệnh trên **GCE** theo trình tự như sau:
	- **sudo gcloud auth configure-docker <region>-docker.pkg.dev**: Xác thực VM với kho lưu trữ **GAR**
	
	-  **cd ~\/deploy**: đi vào thư mục deploy của VM

	- **sudo docker compose -f compose.prod.yml pull**: tải image từ kho lưu trữ **GAR** về VM

	- **sudo docker compose -f compose.prod.yml up -d**: xây dựng và chạy các container từ image đã tải về VM

	- **sudo docker image prune -f**: xóa các images lửng lơ trên VM
