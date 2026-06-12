<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Admin\Services\Post\PostServiceInterface;
use App\Models\Post;

use App\Admin\Repositories\Setting\SettingRepositoryInterface;

class PostController extends Controller
{
    protected $repository;
    protected $service;
    protected $categoryRepository;
    protected $model;
    protected SettingRepositoryInterface $settingRepository;

    public function __construct(
        PostRepositoryInterface $repository,
        PostServiceInterface $service,
        PostCategoryRepositoryInterface $categoryRepository,
        Post $model,
        SettingRepositoryInterface $settingRepository
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
        $this->categoryRepository = $categoryRepository;
        $this->model = $model;
        $this->settingRepository = $settingRepository;
    }

    public function getView(): array
    {
        return [
            'index' => 'user.posts.index',
            'detail' => 'user.posts.detail',
            'category' => 'user.posts.category',
        ];
    }

    public function getRoute(): array
    {
        return [];
    }

    public function category($slug)
    {
        $query = $this->model->query();
        $id = $this->categoryRepository->getQueryBuilder()->where('slug', $slug)->first()->id;
        $category = $this->categoryRepository->findOrFail($id);
        $posts = $this->model->scopeHasCategories($query, [$id])->paginate(3);
        $postIds = $posts->pluck('id');
        $otherPosts = $this->model->whereNotIn('id', $postIds)->limit(3)->get();
        $categories = $this->categoryRepository->getFlatTree();
        return view($this->view['category'], [
            'posts' => $posts,
            'categories' => $categories,
            'otherPosts' => $otherPosts,
            'category' => $category,
            'breadcrumbs' => $this->homeCrums->add(__('Tin tức'), route('user.post.index'))->add(__($category->name))->getBreadcrumbs()
        ]);
    }

    public function detail($slug)
    {
        $post = $this->model->where('slug', $slug)->first();
        $relatedPosts = $this->model->whereHas('categories', function ($query) use ($post) {
            $query->whereIn('category_id', $post->categories->pluck('id'));
        })->where('id', '!=', $post->id)->limit(3)->get();
        $categories = $this->categoryRepository->getFlatTree();
        return view($this->view['detail'], [
            'post' => $post,
            'categories' => $categories,
            'relatedPosts' => $relatedPosts,
            'breadcrumbs' => $this->homeCrums->add(__('Tin tức'), route('user.post.index'))->add(__('Chi tiết bài viết'))->getBreadcrumbs()
        ]);
    }

    public function index($slug = null)
    {
        $query = $this->model->query();

        if ($slug) {
            $id = $this->categoryRepository->getQueryBuilder()->where('slug', $slug)->first()->id;
            $posts = $this->model->scopeHasCategories($query, [$id])->get();
            $category = $this->categoryRepository->findOrFail($id);
        } else {
            $posts = $this->model->scopePublished($query)
                ->orderByRaw('is_featured ASC, posted_at DESC')->get();
        }

        $settings = $this->settingRepository->getAll();
        $title = $settings->where('setting_key', 'post_title')->first()->plain_value;
        $meta_desc = $settings->where('setting_key', 'post_meta_desc')->first()->plain_value;
        $breadcrumbs = $this->homeCrums->add(__('Tin tức'))->getBreadcrumbs();
        return view($this->view['index'], compact('posts', 'title', 'meta_desc', 'breadcrumbs'));
    }
}
