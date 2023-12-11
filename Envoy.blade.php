@servers(['web' => 'u313532741@153.92.220.139 -p 65002'])

@setup
$password = isset($password) ? $password : 'Not Defined';

$branch = isset($branch) ? $branch : "testing";


/*
|--------------------------------------------------------------------------
| Git Config
|--------------------------------------------------------------------------
|
| The git repository location.
|
*/
echo 'Git Config========================================';
//$repository = 'git@github.com:fh32000/web-tools.git';
$repository="https://github.com/alihesahm/TripBuddy.git";

$server_dir=$branch;
/*
|--------------------------------------------------------------------------
| Server Paths
|--------------------------------------------------------------------------
|
| The base paths where the deployment will happens.
|
*/
$app_dir = "/home/u313532741/domains/orangered-magpie-961382.builder-preview1.com";
$releases_dir = "$app_dir/$server_dir/releases";
$release = date('YmdHis');
$new_release_dir ="$releases_dir/$release";
$branch_path="$app_dir/$branch";
$storage_path="$branch_path/storage";
$env_file_name =".env.$branch";
$env_path="$branch_path/$env_file_name";
echo '{{$env_path}}';


/*
|--------------------------------------------------------------------------
| Num of releases
|--------------------------------------------------------------------------
|
| The number of releases to keep.
|
*/

$keep = 3;

@endsetup

{{-- @story('deploy')
deploy
@endstory --}}

@task('deploy')
echo 'The password is: {{ $password }}.';
echo 'Start Task Cloning repository'
sync && free -g -h
echo 'Cloning  branch {{ $branch }} from rep {{ $repository }} in {{ $new_release_dir }}'
[ -d {{ $new_release_dir }} ] || mkdir -p "{{$new_release_dir}}" ;
ssh-keygen -R github.com && ssh-keyscan github.com >> ~/.ssh/known_hosts
{{-- ssh -o HostKeyAlgorithms=+ssh-rsa,ssh-dss -i host_privetkey -p 21098 rakevqpc@199.188.201.124 --}}

git clone --depth 1 --branch {{ $branch }} {{ $repository }} {{ $new_release_dir }}
cd {{ $new_release_dir }}
git reset --hard {{ $commit }}

echo 'Start Task Run Composer ({{ $release }})'
pwd
cd {{ $new_release_dir }}
{{--composer update--}}
{{--php composer.phar update--}}
echo "install composer ({{ $release }})"
php composer.phar install --no-interaction --prefer-dist --optimize-autoloader -q --no-ansi --no-scripts --no-progress > /dev/null
php composer.phar dumpautoload
echo "composer installed  for ({{ $release }})"

echo "Linking new release  ({{ $release }}) to current directory"
branch={{$branch}}

current="{{ $app_dir }}/{{ $server_dir }}/current"


@php
    $is_storage_exist= file_exists($storage_path);
@endphp

@if (!$is_storage_exist)
    echo 'storage files doesnt-exist in {{$storage_path}} we ctare it for yue';
    cp -R {{$new_release_dir}}/storage {{$branch_path}}
@endif


echo "crate public folder for {{ $branch }} if not exist"
mkdir -p "{{ $app_dir}}/testing.rakeb.fun"

@if ($branch!='main')
    @php
        $public_folder_path="$app_dir/testing.rakeb.fun/";
    @endphp
    echo "crate public folder for {{ $branch }} if not exist"
    mkdir -p "{{ $app_dir}}/testing.rakeb.fun"

@else

    @php
        $public_folder_path="$app_dir/public_html/";
    @endphp
@endif



{{-- echo "copy build to public "
mkdir -p {{ $new_release_dir}}/public/build/
cp -r -u  -T  {{$branch_path}}/build/.  {{ $new_release_dir}}/public/build/ --}}

echo "copy all files from public app folder with hidden files to public {{ $branch }}  domain folder path"
cp -r -u -v  -T {{$new_release_dir}}/public/. {{$public_folder_path}}

echo "copy {{"index.$branch.php"}} to public"
cp -Rf  {{$new_release_dir}}/public/{{"index.$branch.php"}} {{$public_folder_path}}/index.php

ln -nfs {{ $app_dir }}/{{$branch}}/storage  {{ $new_release_dir }}/public


{{--// overate robots in non production environments--}}
@if ($branch!='main')
    cp -r -u  -T  {{$new_release_dir}}/public/disallow-all-robots.txt {{ $public_folder_path }}/robots.txt
@endif

echo 'Copy .env file'

@php
    $is_env_exist= file_exists($env_path)
@endphp

@if (!$is_env_exist)
    echo '{{$env_file_name}} doesnt-exist in {{$env_path}} we ctare empty one yue have to modefiy et ';
    touch "{{$env_path}}"
@endif

cp {{ $app_dir }}/{{$branch}}/{{$env_file_name}}  {{ $new_release_dir }}/.env
echo 'Linking current release'
{{--chown -R ubuntu:www-data  {{ $new_release_dir }}--}}
find {{ $new_release_dir }} -type f -exec chmod 664 {} \;
find {{ $new_release_dir }} -type d -exec chmod 775 {} \;
echo {{$new_release_dir}}
echo {{$current}}
ln -nfs {{ $new_release_dir }} $current
cd $current/public

echo 'Linking Storage'

rm -rf {{ $new_release_dir }}/storage
ln -nfs {{ $storage_path }} {{ $new_release_dir }}/public/storage
ln -nfs {{ $storage_path }} {{ $new_release_dir }}
ln -nfs {{ $storage_path }}/app/public/ {{ $public_folder_path }}/storage

echo {{$app_dir}};


echo "Setting up the app"
cd {{ $new_release_dir }}
pwd
sync && free -g -h
{{--php artisan key:generate --force--}}

{{--# Get the code version from Git--}}
APP_VERSION=$(git rev-parse --short HEAD)
{{--# Write the code version to the environment file--}}
echo "APP_VERSION=${APP_VERSION}" >> .env

echo "Run migrate"
php artisan migrate:fresh --force --seed
php artisan view:clear
{{--php artisan responsecache:clear--}}
{{-- php artisan cache:app-v --}}
php artisan optimize
php artisan event:cache
{{--php artisan horizon:terminate--}}
{{--sudo supervisorctl reread--}}
{{--sudo service supervisor restart--}}
{{--php artisan public:s3--}}
sync
{{--npm install--}}
{{--npm run prod--}}


echo "Clean old releases";
cd {{ $releases_dir }};

echo "we will keep only last {{ $keep }} releases || and will remove >>  $(ls -t | tail -n +{{ $keep +1 }})";

rm -rf $(ls -t | tail -n +{{ $keep +1 }});


sync && free -g -h -t
echo 'Deployment completed successfully. the new {{$branch}} releases {{$release}} is live now :) '
cd {{ $new_release_dir }}
{{--php artisan app:deploy-notifcation-command--}}
@endtask

@task('run_composer')
echo 'Start Task Run Composer ({{ $release }})'
pwd
cd {{ $new_release_dir }}
{{--composer update--}}
{{--php composer.phar update--}}
echo "install composer ({{ $release }})"
php composer.phar install --no-interaction --prefer-dist --optimize-autoloader -q --no-ansi --no-scripts --no-progress > /dev/null
php composer.phar dumpautoload
echo "composer installed  for ({{ $release }})"
@endtask

@task('update_symlinks')
echo "Linking new release  ({{ $release }}) to current directory"
branch={{$branch}}

current="{{ $app_dir }}/{{ $server_dir }}/current"


@php
    $is_storage_exist= file_exists($storage_path);
@endphp

@if (!$is_storage_exist)
    echo 'storage files doesnt-exist in {{$storage_path}} we ctare it for yue';
    cp -R {{$new_release_dir}}/storage {{$branch_path}}
@endif


echo "crate public folder for {{ $branch }} if not exist"
mkdir -p "{{ $app_dir}}/testing.rakeb.fun"

@if ($branch!='main')
    @php
        $public_folder_path="$app_dir/testing.rakeb.fun/";
    @endphp
    echo "crate public folder for {{ $branch }} if not exist"
    mkdir -p "{{ $app_dir}}/testing.rakeb.fun"

@else

    @php
        $public_folder_path="$app_dir/public_html/";
    @endphp
@endif



{{-- echo "copy build to public "
mkdir -p {{ $new_release_dir}}/public/build/
cp -r -u  -T  {{$branch_path}}/build/.  {{ $new_release_dir}}/public/build/ --}}

echo "copy all files from public app folder with hidden files to public {{ $branch }}  domain folder path"
cp -r -u -v  -T {{$new_release_dir}}/public/. {{$public_folder_path}}

echo "copy {{"index.$branch.php"}} to public"
cp -Rf  {{$new_release_dir}}/public/{{"index.$branch.php"}} {{$public_folder_path}}/index.php

ln -nfs {{ $app_dir }}/{{$branch}}/storage  {{ $new_release_dir }}/public


{{--// overate robots in non production environments--}}
@if ($branch!='main')
    cp -r -u  -T  {{$new_release_dir}}/public/disallow-all-robots.txt {{ $public_folder_path }}/robots.txt
@endif

echo 'Copy .env file'

@php
    $is_env_exist= file_exists($env_path)
@endphp

@if (!$is_env_exist)
    echo '{{$env_file_name}} doesnt-exist in {{$env_path}} we ctare empty one yue have to modefiy et ';
    touch "{{$env_path}}"
@endif

cp {{ $app_dir }}/{{$branch}}/{{$env_file_name}}  {{ $new_release_dir }}/.env
echo 'Linking current release'
{{--chown -R ubuntu:www-data  {{ $new_release_dir }}--}}
find {{ $new_release_dir }} -type f -exec chmod 664 {} \;
find {{ $new_release_dir }} -type d -exec chmod 775 {} \;
echo {{$new_release_dir}}
echo {{$current}}
ln -nfs {{ $new_release_dir }} $current
cd $current/public

echo 'Linking Storage'

rm -rf {{ $new_release_dir }}/storage
ln -nfs {{ $storage_path }} {{ $new_release_dir }}/public/storage
ln -nfs {{ $storage_path }} {{ $new_release_dir }}
ln -nfs {{ $storage_path }}/app/public/ {{ $public_folder_path }}/storage

echo {{$app_dir}};

@endtask

@task('setup_app')
echo "Setting up the app"
cd {{ $new_release_dir }}
pwd
sync && free -g -h
{{--php artisan key:generate --force--}}

{{--# Get the code version from Git--}}
APP_VERSION=$(git rev-parse --short HEAD)
{{--# Write the code version to the environment file--}}
echo "APP_VERSION=${APP_VERSION}" >> .env

echo "Run migrate"
php artisan migrate:fresh --force --seed
php artisan view:clear
php artisan responsecache:clear
{{-- php artisan cache:app-v --}}
php artisan optimize
php artisan event:cache
{{--php artisan horizon:terminate--}}
{{--sudo supervisorctl reread--}}
{{--sudo service supervisor restart--}}
{{--php artisan public:s3--}}
sync
{{--npm install--}}
{{--npm run prod--}}
@endtask

{{-- Clean old releases --}}
@task('clean')
echo "Clean old releases";
cd {{ $releases_dir }};

echo "we will keep only last {{ $keep }} releases || and will remove >>  $(ls -t | tail -n +{{ $keep +1 }})";

rm -rf $(ls -t | tail -n +{{ $keep +1 }});
@endtask

@task('succeed')
sync && free -g -h -t
pwd
echo 'Deployment completed successfully. the new {{$branch}} releases {{$release}} is live now :) '
cd {{ $new_release_dir }};
pwd
php artisan app:deploy-notifcation-command
@endtask
