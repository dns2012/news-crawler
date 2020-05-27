<?php 

namespace Service;

use RedBeanPHP\R;

R::setup( 'mysql:host=crawler_database;dbname=default', 'crawler', 'crawler2020+' );

class DatabaseService 
{
    public static function createPost($data)
    {
        $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower(trim($data['title'])));

        if (R::find('posts', 'slug = :slug', ['slug' => $slug])) {
            return true;
        }

        $post = R::dispense("posts");
        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->image = $data['image'];
        $post->slug = $slug;
        $post->link = $data['link'];
        $post->author = strtoupper($data['author']);
        $post->published_at = $data['published_at'];
        $post->created_at = date('Y-m-d H:i:s');
        $post->updated_at = date('Y-m-d H:i:s');
        
        return R::store( $post );
    }
}