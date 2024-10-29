<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Tag;
use App\Models\Course;

use App\Models\Module;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Courses\CreateCourseRequest;
use App\Http\Requests\Admin\Courses\UpdateCourseRequest;
use App\Http\Requests\Client\Courses\StoreNewCourseRequest;
use App\Http\Requests\Client\Courses\UpdateCourseOverviewRequest;
use App\Http\Requests\Client\Courses\UpdateTargetStudentRequest;
use App\Models\Audience;
use App\Models\Goal;
use App\Models\Requirement;

class CourseController extends Controller
{
    public function index()
    {
        try {
            //Lấy danh sách khoá học của tác giả đó.
            $courses = Course::with('user', 'category', 'tags')->where('id_user', auth()->id())->latest('id')->paginate(8);
            //Khi lấy về đúng
            return response()->json([
                'status' => 200,
                'message' => 'Danh sách khóa học.',
                'data' => $courses,
            ], 200);
        } catch (\Exception $e) {
            //Khi lấy dữ liệu sai
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //Lấy tổng quan khoá học
    public function getCourseOverview(Course $course)
    {
        //Check quyền truy cập
        try {
            if (auth()->id() !== $course->id_user) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền truy cập.',
                    'data' => []
                ], 403);
            }
            //Dữ liệu lấy từ bảng khoá course

            $data = $course->only([
                'name',
                'sort_description',
                'description',
                'level',
                'category',
                'thumbnail',
                'trailer',
                'price',
                'price_sale',
                'is_active',
                'tags',
            ]);
            //Trả về dữ liệu khi lấy thành công
            return response()->json([
                'message' => 'Tổng quan khóa học.',
                'data' => $data,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi lấy tổng quan khóa học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    //Lưu 1 khoá học mới.

    public function storeNewCourse(StoreNewCourseRequest $request)
    {

        //Lấy dữ liệu request đẩy lên
        $data = $request->only(['name', 'id_category']);
        //Lấy người tạo ra khoá học
        $data['id_user'] = auth()->id();
        //Lấy đường dẫn khoá học nhờ vào name và uuid
        $data['slug'] = Str::slug($data['name']) . '_' . Str::uuid();

        try {
            // Tạo 1 khoá học mới
            $newCourse = Course::create($data);
            //Thêm 1 khoá học thành công
            return response()->json([
                'status' => 201,
                'message' => 'Thêm mới khóa học thành công!',
                'data' => $newCourse->load('category')
            ], 201);
        } catch (\Exception $e) {
            //Lưu log
            Log::error($e->getMessage());
            DB::rollBack();
            //Kiểm tra báo lỗi khi lỗi server
            return response()->json([
                'status' => 500,
                'message' => 'Thêm mới không thành công!' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    //Lấy thông tin 1 khoá học theo teacher
    public function showCourseTeacher(Course $course)
    {
        try {
            //check quyền truy cập
            if ($course->status == 'draft' && $course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => "Khóa học này đang ở chế độ nháp, vui lòng liên hệ {$course->user->name}.",
                    'data' => []
                ], 403);
            }
            //Trả về dữ liệu nếu pass quyền truy cập.
            $courseData = $course->load(['user', 'category', 'modules', 'tags', 'goals', 'requirements', 'audiences']);
            //Lấy thành công
            return response()->json([
                'status' => 200,
                'message' => 'Thông tin khóa học.',
                'data' => $courseData,
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    //Cập nhật tổng quan

    public function updateCourseOverview(UpdateCourseOverviewRequest $request, Course $course)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra quyền truy cập
            if ($course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền sửa khóa học này!',
                    'data' => []
                ], 403);
            }

            $data = $request->validated();

            // lưu thumbnail và trailer cũ để xóa nếu cần
            $oldThumbnail = $course->thumbnail;
            $oldTrailer = $course->trailer;

            // gen course code
            if (!$course->code) {
                $data['code'] = 'KH-' . Str::uuid();
            }

            // nếu giá miễn phí
            if (isset($data['price']) && $data['price'] == 0) {
                $data['is_free'] = 1;
                $data['price_sale'] = 0;
            } else {
                $data['is_free'] = 0;
            }

            // thumbnail
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $newNameImage = 'course_thumbnail_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                $pathImage = Storage::putFileAs('courses/thumbnails', $image, $newNameImage);
                $data['thumbnail'] = $pathImage;
            }

            // trailer video
            if ($request->hasFile('trailer')) {
                $video = $request->file('trailer');
                $newNameVideo = 'course_trailer_' . Str::uuid() . '.' . $video->getClientOriginalExtension();
                $pathVideo = Storage::putFileAs('courses/trailers', $video, $newNameVideo);
                $data['trailer'] = $pathVideo;
            }

            $course->update($data);

            // xử lý tags
            $tagIds = [];
            if (isset($data['tags']) && is_array($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        $tagModel = Tag::firstOrCreate([
                            'name' => $tag,
                            'slug' => Str::slug($tag),
                        ]);
                        $tagIds[] = $tagModel->id;
                    }
                }
                $course->tags()->sync($tagIds);
            } else {
                $course->tags()->sync([]); // request không có tag, xoá tất cả tag cũ
            }

            DB::commit();

            // xóa thumbnail/trailer cũ nếu có thumbnail/trailer mới
            if ($oldThumbnail && isset($data['thumbnail'])) {
                Storage::delete($oldThumbnail);
            }

            if ($oldTrailer && isset($data['trailer'])) {
                Storage::delete($oldTrailer);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Đã lưu thành công các thay đổi của bạn.',
                'data' => $course->load('tags')
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();

            // xóa thumbnail/trailer mới nếu gặp lỗi
            if (isset($data['thumbnail'])) {
                Storage::delete($data['thumbnail']);
            }

            if (isset($data['trailer'])) {
                Storage::delete($data['trailer']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi khi cập nhật khóa học',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCourse(Course $course)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra trạng thái nháp
            if ($course->status !== 'draft') {
                return response()->json([
                    'status' => 400,
                    'message' => 'Chỉ có thể xóa khóa học ở trạng thái nháp.',
                    'data' => []
                ], 400);
            }

            // Kiểm tra quyền sở hữu khóa học
            if ($course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền xóa khóa học này.',
                    'data' => []
                ], 403);
            }

            // Xóa khóa học
            $course->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Xóa khóa học thành công.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi xóa khóa học',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Hàm ẩn khóa hoc
    public function disableCourse(Course $course)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra quyền sở hữu khóa học
            if ($course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền ẩn khóa học này.',
                    'data' => []
                ], 403);
            }

            $course->update(['is_active' => 0]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Khóa học đã được ẩn thành công.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi ẩn khóa học: ',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Hiện khóa học
    public function enableCourse(Course $course)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra quyền sở hữu khóa học
            if ($course->id_user !== auth()->id()) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Bạn không có quyền hiện khóa học này.',
                    'data' => []
                ], 403);
            }

            $course->update(['is_active' => 1]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Khóa học đã được kích hoạt hiển thị thành công.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi kích hoạt hiển thị khóa học',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


    public function submit(Course $course)
    {
        try {
            $course->update([
                'status' => 'pending',
                'submited_at' => now()
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Khóa học của bạn đã được gửi đi để xem xét.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Cập nhật không thành công! ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function searchCourses(Request $request)
    {

        // Số thứ tự trang;
        $page = $request->page ?? 1;
        // Số bản ghi trên một trang;
        $perPage = $request->perPage ?? 12;

        // Lấy keyword ở url;
        $searchTerm = $request->key;

        $courses = DB::table('courses as c')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                c.id as course_id,
                c.name as course_name,
                c.thumbnail as course_thumbnail,
                c.total_student,
                COUNT(DISTINCT l.id) as total_lessons,
                c.duration as course_duration,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->join('users as u', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->leftJoin('modules as m', 'm.id_course', '=', 'c.id')
            ->leftJoin('lessons as l', 'l.id_module', '=', 'm.id')
            ->where('c.is_active', 1)
            ->where('u.is_active', 1)
            ->where('u.user_type', 'teacher')
            ->where(function ($query) use ($searchTerm) {
                $query->where('c.name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('u.name', 'LIKE', "%{$searchTerm}%");
            })
            ->groupBy('u.id', 'u.name', 'u.avatar', 'c.id', 'c.name', 'c.thumbnail', 'c.total_student', 'c.duration')
            ->orderByDesc('average_rating')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($courses->count() <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found',
            ], 204);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'courses' => $courses->items(),
                'current_page' => $courses->currentPage(),
                'total_pages' => $courses->lastPage(),
                'total_count' => $courses->total(),
            ]
        ], 200);
    }

    public function courseCheckout(Request $request)
    {
        $slug = $request->slug;

        $course = DB::table('courses as c')
            ->selectRaw('
                u.id as user_id,
                u.name as user_name,
                u.avatar as user_avatar,
                c.id as course_id,
                c.name as course_name,
                c.thumbnail as course_thumbnail,
                c.price,
                c.price_sale,
                c.total_student,
                COUNT(DISTINCT l.id) as total_lessons,
                c.duration as course_duration,
                ROUND(IFNULL(AVG(r.rate), 0), 1) as average_rating
            ')
            ->join('users as u', 'u.id', '=', 'c.id_user')
            ->leftJoin('ratings as r', 'c.id', '=', 'r.id_course')
            ->leftJoin('modules as m', 'm.id_course', '=', 'c.id')
            ->leftJoin('lessons as l', 'l.id_module', '=', 'm.id')
            ->where('c.is_active', 1)
            ->where('c.slug', $slug)
            ->groupBy('u.id', 'u.name', 'u.avatar', 'c.id', 'c.name', 'c.thumbnail', 'c.total_student', 'c.duration')
            ->first();

        if (!$course) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Khóa học không tồn tại'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Truy vấn khóa học thành công',
            'data' => $course
        ], 200);
    }
}
