<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\ChapterController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\ReadingController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\ReadLaterController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactMessageController;

////////////////////////////////////////routes about user
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('users/reset-password', [UserController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('users/reset', [UserController::class, 'resetPassword'])->name('password.reset');

Route::post('users/email/resend', [VerificationController::class, 'resend'])
    ->middleware('auth:sanctum');

Route::post('/users/select-categories-and-get-stories', [UserController::class, 'selectCategoriesAndGetStories']);///// not updated in server 

 Route::put('/user/update', [UserController::class, 'updateUser'])->middleware('auth:sanctum');///// not updated in server 

Route::post('users/login', [UserController::class, 'login']);
Route::post('users/register', [UserController::class, 'register']);
Route::get('users/currentuser', [UserController::class, 'getUser'])->middleware('auth:sanctum');///
Route::post('/users/logout', [UserController::class, 'logoutFromOneDevice'])->middleware('auth:sanctum');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
Route::post('users/update-password', [UserController::class , 'updatePassword'])->middleware('auth:sanctum');
Route::apiResource('Chapters', ChapterController::class)->middleware('auth:sanctum');
Route::apiResource('Reviews', ReviewController::class)->middleware('auth:sanctum');
Route::apiResource('Reading', ReadingController::class)->middleware('auth:sanctum');
Route::apiResource('Tags', TagController::class)->middleware('auth:sanctum');
Route::apiResource('readlater', ReadLaterController::class)->middleware('auth:sanctum');
Route::apiResource('contact-messages', ContactMessageController::class); //done
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('stories', StoryController::class)->only(['store']); 
    Route::post('/stories/update', [StoryController::class, 'updateStory']); 
    Route::delete('stories/{story}', [StoryController::class, 'destroy']);
    Route::delete('stories/{id}/force-delete', [StoryController::class, 'forceDestroy']); 
});
Route::get('stories/{story}', [StoryController::class, 'show']);
Route::get('/stories', [StoryController::class, 'getAllStories']);
Route::get('/stories/restore/{story_id}',
[StoryController::class, 'restore'])
->middleware('auth:sanctum');
Route::get('stories/bycategory/most-rate/{category}', [StoryController::class , 'getTopStoriesByCategory']);
Route::get('/stories/myStory', [StoryController::class , 'getMyStory'])->middleware('auth:sanctum');
Route::get('stories/byCategory/{category}', [StoryController::class, 'getStoriesByCategory']);
Route::get('/categories/{category}/completed-stories', [StoryController::class, 'getCompletedStoriesByCategory']);
Route::get('stories/{category_id}/not-paid-chapters', [StoryController::class, 'getStoriesWithNotPaidChapters']);
Route::get('/stories/readlater/all', [StoryController::class , 'storiesinReadLater'])->middleware('auth:sanctum');
Route::get('/categories/{category}/top-stories', [StoryController::class, 'getTopViewedStoriesWithTags']);
Route::get('/tags/{tag}/stories', [StoryController::class, 'getStoriesByTag']);
Route::get('/latest-stories', [StoryController::class, 'getadvertisementStoryByLatestStory']);
Route::get('story-details/{id}', [StoryController::class, 'storyDetails']);
Route::get('/stories/mystory', [StoryController::class , 'getMyStory'])->middleware('auth:sanctum');//// not found 

Route::middleware('auth:sanctum')->group(function () {

    Route::put('/stories/{storyId}/publish', [StoryController::class, 'publishStory']);

    Route::get('/stories/published', [StoryController::class, 'getPublishedStories']);/////// notfound
});
Route::get('/stories/deleted',
[StoryController::class, 'getAlldeleted'])
->middleware('auth:sanctum');///////// not found

//////////////////////////route about category
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class)->except('index');
});

Route::get('categories', [CategoryController::class, 'index']);


///////////////////////////////routes about chapter

Route::prefix('stories/{story}')->group(function () {
   
    Route::get('chapters', [ChapterController::class, 'index'])->middleware('auth:sanctum');
    Route::get('chapters/{chapter}', [ChapterController::class, 'show'])->middleware('auth:sanctum');
    Route::delete('chapters/{chapter}', [ChapterController::class, 'destroy'])->middleware('auth:sanctum');
});


////////////////////////////////////////////////routes about readlater 

Route::post('read_later/myreadlater_list', [ReadLaterController::class , 'inReadlater'])->middleware('auth:sanctum');
Route::delete('readlater_lists/remove/{story_id}', [ReadLaterController::class , 'removeReadLaterlist'])->middleware('auth:sanctum');
////////////////////////////////////////////////////routes about tag 
Route::post('/stories/{story_id}/tags', [TagController::class, 'storeStoryTags'])->middleware('auth:sanctum');
Route::get('/category/{categoryId}/tags', [TagController::class, 'getTagsByCategory']);
// Route::post('/stories-by-tags', [TagController::class, 'getStoriesByTags']);
Route::post('/stories/{storyId}/tags/remove', [TagController::class, 'removeTagsFromStory'])->middleware('auth:sanctum');
Route::put('/stories/{story}/tags', [TagController::class, 'updateStoryTags'])->middleware('auth:sanctum');
//////////////////////////////////////////////////routes about reviews
Route::post('/stories/{storyId}/vote', [ReviewController::class, 'toggleVote'])->middleware('auth:sanctum');
Route::post('/stories/{storyId}/feedback', [ReviewController::class, 'addFeedback'])->middleware('auth:sanctum');
Route::delete('/stories/{storyId}/feedback', [ReviewController::class, 'deleteFeedback'])->middleware('auth:sanctum');
Route::get('reviews/story/{story_id}', [ReviewController::class, 'getAllReviews']);
Route::post('/reviews/{reviewId}/block', [ReviewController::class, 'blockFeedback'])->middleware('auth:sanctum');
Route::get('/stories/{storyId}/feedback', [ReviewController::class, 'getFeedbackActive']);
Route::delete('stories/{story_id}/reviews/{review_id}', [ReviewController::class, 'destroy'])->middleware('auth:sanctum');
////////////////////////////////////////////////routes abou reading 
Route::post('/stories/{storyId}/start-reading', [ReadingController::class, 'startReading'])->middleware('auth:sanctum');
// Route::delete('tags/{tagId}', [TagController::class, 'deletingTag'])->middleware('auth:sanctum');
// Route::put('tags/{tagId}', [TagController::class, 'updateTag'])->middleware('auth:sanctum');
// Route::delete('/stories/{storyId}/review', [ReviewController::class, 'deleteReview'])->middleware('auth:sanctum');




















// GET|HEAD        / .......................................................................................................... 
// GET|HEAD        api/Chapters .................................................. Chapters.index › Api\ChapterController@index 
// POST            api/Chapters .................................................. Chapters.store › Api\ChapterController@store 
// GET|HEAD        api/Chapters/{Chapter} .......................................... Chapters.show › Api\ChapterController@show 
// PUT|PATCH       api/Chapters/{Chapter} ...................................... Chapters.update › Api\ChapterController@update 
// DELETE          api/Chapters/{Chapter} .................................... Chapters.destroy › Api\ChapterController@destroy 
// GET|HEAD        api/Reading .................................................... Reading.index › Api\ReadingController@index 
// POST            api/Reading .................................................... Reading.store › Api\ReadingController@store 
// GET|HEAD        api/Reading/{Reading} ............................................ Reading.show › Api\ReadingController@show 
// PUT|PATCH       api/Reading/{Reading} ........................................ Reading.update › Api\ReadingController@update  
// DELETE          api/Reading/{Reading} ...................................... Reading.destroy › Api\ReadingController@destroy  
// GET|HEAD        api/Reviews ..................................................... Reviews.index › Api\ReviewController@index  
// POST            api/Reviews ..................................................... Reviews.store › Api\ReviewController@store  
// GET|HEAD        api/Reviews/{Review} .............................................. Reviews.show › Api\ReviewController@show  
// PUT|PATCH       api/Reviews/{Review} .......................................... Reviews.update › Api\ReviewController@update  
// DELETE          api/Reviews/{Review} ........................................ Reviews.destroy › Api\ReviewController@destroy  
// POST            api/Stories ...................................................... Stories.store › Api\StoryController@store  
// PUT|PATCH       api/Stories/{Story} ............................................ Stories.update › Api\StoryController@update  
// DELETE          api/Stories/{Story} .......................................... Stories.destroy › Api\StoryController@destroy  
// GET|HEAD        api/Tags .............................................................. Tags.index › Api\TagController@index  
// POST            api/Tags .............................................................. Tags.store › Api\TagController@store  
// GET|HEAD        api/Tags/{Tag} .......................................................... Tags.show › Api\TagController@show  
// PUT|PATCH       api/Tags/{Tag} ...................................................... Tags.update › Api\TagController@update  
// DELETE          api/Tags/{Tag} .................................................... Tags.destroy › Api\TagController@destroy  
// GET|HEAD        api/categories ............................................. categories.index › Api\CategoryController@index  
// POST            api/categories ............................................. categories.store › Api\CategoryController@store
// POST            api/categories/update ........................................................ Api\CategoryController@update  
// GET|HEAD        api/categories/{category} .................................... categories.show › Api\CategoryController@show  
// PUT|PATCH       api/categories/{category} ................................ categories.update › Api\CategoryController@update  
// DELETE          api/categories/{category} .............................. categories.destroy › Api\CategoryController@destroy  
// GET|HEAD        api/categories/{category}/completed-stories .............. Api\StoryController@getCompletedStoriesByCategory  
// GET|HEAD        api/category/{categoryId}/tags ......................................... Api\TagController@getTagsByCategory  
// GET|HEAD        api/contact-messages ........................... contact-messages.index › Api\ContactMessageController@index  
// POST            api/contact-messages ........................... contact-messages.store › Api\ContactMessageController@store  
// GET|HEAD        api/contact-messages/{contact_message} ........... contact-messages.show › Api\ContactMessageController@show  
// PUT|PATCH       api/contact-messages/{contact_message} ....... contact-messages.update › Api\ContactMessageController@update  
// DELETE          api/contact-messages/{contact_message} ..... contact-messages.destroy › Api\ContactMessageController@destroy  
// POST            api/read_later/myreadlater_list ........................................ Api\ReadLaterController@inReadlater
// GET|HEAD        api/readlater .............................................. readlater.index › Api\ReadLaterController@index  
// POST            api/readlater .............................................. readlater.store › Api\ReadLaterController@store  
// GET|HEAD        api/readlater/{readlater} .................................... readlater.show › Api\ReadLaterController@show  
// PUT|PATCH       api/readlater/{readlater} ................................ readlater.update › Api\ReadLaterController@update  
// DELETE          api/readlater/{readlater} .............................. readlater.destroy › Api\ReadLaterController@destroy  
// DELETE          api/readlater_lists/remove/{story_id} .......................... Api\ReadLaterController@removeReadLaterlist  
// GET|HEAD        api/reviews/story/{story_id} ............................................ Api\ReviewController@getAllReviews  
// POST            api/stories-by-tags ..................................................... Api\TagController@getStoriesByTags  
// GET|HEAD        api/stories/byCategory/{category} ................................. Api\StoryController@getStoriesByCategory
// GET|HEAD        api/stories/bycategory/most-rate/{category} .................... Api\StoryController@getTopStoriesByCategory  
// GET|HEAD        api/stories/myStory ......................................................... Api\StoryController@getMyStory  
// GET|HEAD        api/stories/readlater/all ........................................... Api\StoryController@storiesinReadLater  
// GET|HEAD        api/stories/{category_id}/not-paid-chapters .............. Api\StoryController@getStoriesWithNotPaidChapters  
// POST            api/stories/{storyId}/tags/remove .................................... Api\TagController@removeTagsFromStory  
// GET|HEAD        api/stories/{story_id}/chapters ................................................ Api\ChapterController@index
// DELETE          api/stories/{story_id}/chapters/{chapter_id} ................................. Api\ChapterController@destroy  
// DELETE          api/stories/{story_id}/reviews/{review_id} .................................... Api\ReviewController@destroy  
// POST            api/stories/{story_id}/tags ............................................... Api\TagController@storeStoryTags  
// GET|HEAD        api/stories/{story}/chapters/{chapter} .......................................... Api\ChapterController@show  
// GET|HEAD        api/user ...................................................................................................  
// GET|HEAD        api/users ........................................................... users.index › Api\UserController@index  
// POST            api/users ........................................................... users.store › Api\UserController@store  
// POST            api/users/login ................................................................... Api\UserController@login
// POST            api/users/logout .................................................... Api\UserController@logoutFromOneDevice  
// GET|HEAD        api/users/me .................................................................... Api\UserController@getUser  
// POST            api/users/register ............................................................. Api\UserController@register  
// GET|HEAD        api/users/{user} ...................................................... users.show › Api\UserController@show  
// PUT|PATCH       api/users/{user} .................................................. users.update › Api\UserController@update  
// DELETE          api/users/{user} ................................................ users.destroy › Api\UserController@destroy  
// GET|HEAD        sanctum/csrf-cookie ...................... sanctum.csrf-cookie › Laravel\Sanctum › CsrfCookieController@show  
// GET|HEAD        storage/{path} ............................................................................... storage.local  
// GET|HEAD        up .........................................................................................................  

                                                              
                                                                 