<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Constant;
use App\Common\HeaderHelper;
use App\DataTransform\Gallery\ModelToDTO as DataTransform;
use App\DTO\CommonJsonResponse;
use App\Kernel\Abstract\AbstractController;
use App\Kernel\Attribute\Middleware;
use App\Kernel\Component\JsonResponse;
use App\Kernel\Component\Request;
use App\Kernel\Component\Response;
use App\Kernel\Exception\BadRequestHttpException;
use App\Middleware\AuthMiddleware;
use App\Middleware\ConfirmMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\SecurityMiddleware;
use App\Model\Gallery;
use App\Model\Relation;
use App\Model\User;
use App\Service\Logger;

class GalleryController extends AbstractController
{
    public function __construct(
        private readonly DataTransform $dataTransform,
    ) {
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function index(Request $request): JsonResponse
    {
        $securityUser = $request->request->get('security_user');

        $limit = (int)($request->query->get('items_per_page') ?? 9);
        $page = (int)($request->query->get('page') ?? 1);
        $chunk = (int)($request->query->get('chunk') ?? 0);
        $pagination = $request->query->get('pagination') !== 'false' ?? true;
        $userId = $request->query->get('user_id') ?? null;
        $author = $request->query->get('author') ?? null;
        $offset = $limit * ($page - 1);

        if ($author) {
            $gallery = Gallery::findBy(
                ['user_id:eq' => (int)$author],
                ['created_at' => 'DESC'],
            );
        } else if ($securityUser === (int)$userId) {
            $galleryIds = array_map(
                callback: static fn(Relation $relation) => $relation->getGallery()->getId(),
                array: Relation::findBy(['user_id:eq' => (int)$userId])
            );
            $gallery = count($galleryIds)
                ? Gallery::findBy(['id:in' => $galleryIds], ['created_at' => 'DESC'])
                : [];
        } else {
            $gallery = Gallery::findAll(['created_at' => 'DESC']);
        }

        $count = count($gallery);
        if ($pagination && $limit > 0) {
            $gallery = array_slice($gallery, $offset, $limit);
        }

        $gallery = array_values($gallery);

        $gallery = array_map(
            callback: function (Gallery $gallery) use ($request) {
                return $this->dataTransform->transform($gallery, $request);
            },
            array: $gallery
        );

        if ($chunk) {
            $gallery = array_chunk($gallery, 3);
        }

        return $this->json(
            data: $gallery,
            status: Response::HTTP_OK,
            headers: [
                'Content-Range' => sprintf(
                    'gallery %d-%d/%d',
                    $offset,
                    $pagination && $limit > 0 ? min($offset + $limit, $count) : $count,
                    $count
                )
            ],
        );
    }

    public function show(Request $request): JsonResponse
    {
        $galleryId = $request->params[0];
        if (!is_numeric($galleryId)) {
            $message = 'Slug must contain id.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $gallery = Gallery::find((int) $galleryId);
        if (!$gallery) {
            $message = 'Gallery not found.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            $this->dataTransform->transform($gallery, $request, [
                'show_comments' => true,
            ])
        );
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function delete(Request $request): JsonResponse
    {
        global $config;

        if ($request->getContentType() !== 'application/json') {
            throw new BadRequestHttpException('Content-Type must be application/json');
        }

        $galleryId = $request->params[0];
        if (!is_numeric($galleryId)) {
            $message = 'Slug must contain id.';
            return $this->json(['message' => $message], Response::HTTP_BAD_REQUEST);
        }

        $securityUser = $request->request->get('security_user');
        $gallery = Gallery::find((int) $galleryId);
        if ($gallery->getUser()->getId() !== $securityUser) {
            $message = 'Access denied';
            return $this->json(['message' => $message], Response::HTTP_FORBIDDEN);
        }

        try {
            $url = parse_url($gallery->getFile());
            if (isset($url['query']) && isset($url['path'])) {
                $queryParams = [];
                parse_str($url['query'], $queryParams);
                if (isset($queryParams['direction'])) {
                    $var = $config['var_path'];
                    $filename = base64_decode(str_replace(Constant::IMAGE_PATH, '', $url['path']));
                    $direction = $queryParams['direction'];
                    if (file_exists($var.'/'.$direction.'/'.$filename)) {
                        unlink($var.'/'.$direction.'/'.$filename);
                    }
                }
            }
            $gallery->delete();
        } catch (\Throwable $exception) {
            $message = 'Delete gallery handle error.';
            Logger::error(
                message: $message,
                context: [
                    'user_id' => $securityUser,
                    'gallery_id' => $gallery->getId(),
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':'. $exception->getLine(),
                    'exception_trace' => $exception->getTraceAsString(),
                ],
            );

            return $this->json(['message' => $message], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(new CommonJsonResponse(true));
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    public function getAllStickers(): JsonResponse
    {
        global $config;

        if (!is_dir($config['var_path'])) {
            return $this->json([]);
        }

        return $this->json(
            array_values(
                array_map(
                    callback: static fn(string $filename) => Constant::IMAGE_PATH.base64_encode($filename),
                    array: array_diff(
                        scandir($config['var_path'].'/'.Constant::STICKERS_PATH),
                        ['.', '..'],
                    ),
                )
            )
        );
    }

    #[Middleware(class: SecurityMiddleware::class)]
    public function getImageData(Request $request): Response|JsonResponse
    {
        global $config;

        $direction = $request->query->get('direction');

        $var = $config['var_path'].'/'.$direction;
        $name = $request->params[0];

        if (!$name) {
            return $this->json(['message' => 'Sticker not found.'], Response::HTTP_BAD_REQUEST);
        }

        $name = base64_decode($name);
        $images = array_diff(scandir($var), ['.', '..'],);

        $image = array_filter(
            array: $images,
            callback: static fn (string $filename) => $name === $filename,
        );

        if (!$image || count($image) !== 1) {
            return $this->json(['message' => 'Sticker not found.'], Response::HTTP_BAD_REQUEST);
        }

        $image = array_shift($image);
        $fileContent = file_get_contents($var.'/'.$image);
        $mimeContentType = mime_content_type($var.'/'.$image);

        $response = new Response($fileContent);
        $response->addHeader('Content-Type', $mimeContentType);

        $disposition = HeaderHelper::makeDisposition(
            HeaderHelper::DISPOSITION_ATTACHMENT,
            sprintf('image-%s', $image)
        );

        $response->addHeader('Content-Disposition', $disposition);

        return $response;
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function uploadSticker(Request $request): JsonResponse
    {
        global $config;

        $var = $config['var_path'].'/'.Constant::STICKERS_PATH;
        $file = $request->files->get('image');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return $this->json(['message' => 'Image is not uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $imageType = mime_content_type($file['tmp_name']);
            if (!in_array($imageType, Constant::ALLOWED_TYPES, true)) {
                return $this->json(['message' => 'Unsupported image format.'], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Throwable $exception) {
            $message = 'Image upload error.';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':' . $exception->getLine(),
                ]
            );

            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $fileInfo = pathinfo($file['name']);
        $extension = $fileInfo['extension'];
        $newFileName = time() . '.' . $extension;
        $uploadFile = $var.'/'.$newFileName;

        if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return $this->json(['message' => 'Move file error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            new CommonJsonResponse(
                success: true,
                message: Constant::IMAGE_PATH.base64_encode($newFileName)
            )
        );
    }

    #[Middleware(class: SecurityMiddleware::class)]
    #[Middleware(class: AuthMiddleware::class)]
    #[Middleware(class: CsrfMiddleware::class)]
    public function uploadGallery(Request $request): JsonResponse
    {
        global $config;

        $var = $config['var_path'].'/'.Constant::GALLERY_PATH;

        $files = $request->files->get('image');
        if (!$files) {
            $files = $request->files->get('image[]');
        }

        if (!$files) {
            return $this->json(['message' => 'Image is not uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        $normalized = [];
        if (isset($files['tmp_name']) && !is_array($files['tmp_name'])) {
            $normalized[] = $files;
        } else if (isset($files['tmp_name']) && is_array($files['tmp_name'])) {
            foreach ($files['tmp_name'] as $idx => $tmp) {
                $normalized[] = [
                    'name' => $files['name'][$idx] ?? null,
                    'type' => $files['type'][$idx] ?? null,
                    'tmp_name' => $files['tmp_name'][$idx] ?? null,
                    'error' => $files['error'][$idx] ?? null,
                    'size' => $files['size'][$idx] ?? null,
                ];
            }
        } else if (is_array($files)) {
            foreach ($files as $file) {
                if (isset($file['tmp_name'])) $normalized[] = $file;
            }
        }

        if (count($normalized) === 0) {
            return $this->json(['message' => 'Image is not uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        if (!is_dir($var)) {
            mkdir($var, 0777, true);
        }

        $created = [];
        $movedFiles = [];

        try {
            $securityUser = $request->request->get('security_user');
            $user = User::find($securityUser);

            foreach ($normalized as $file) {
                if (!$file || ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                    throw new \RuntimeException('Image is not uploaded.');
                }

                $imageType = mime_content_type($file['tmp_name']);
                if (!in_array($imageType, Constant::ALLOWED_TYPES, true)) {
                    throw new \RuntimeException('Unsupported image format.');
                }

                $fileInfo = pathinfo($file['name'] ?? '');
                $extension = $fileInfo['extension'] ?? 'jpg';
                $newFileName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
                $uploadFile = $var.'/'.$newFileName;

                if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    throw new \RuntimeException('Move file error.');
                }

                $movedFiles[] = $uploadFile;

                $filePath = Constant::IMAGE_PATH.base64_encode($newFileName);

                $gallery = new Gallery();
                $gallery->setUser($user);
                $gallery->setFile($filePath.'?direction='.Constant::GALLERY_PATH);
                $gallery->save();

                $created[] = $this->dataTransform->transform($gallery, $request);
            }
        } catch (\Throwable $exception) {
            foreach ($movedFiles as $f) {
                try { if (file_exists($f)) unlink($f); } catch (\Throwable) {}
            }

            $message = 'Image upload error.';
            Logger::error(
                message: $message,
                context: [
                    'exception_message' => $exception->getMessage(),
                    'exception_place' => $exception->getFile() . ':' . $exception->getLine(),
                ]
            );

            return $this->json(['message' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($created, Response::HTTP_OK);
    }

    private function resizeImageWithAlpha($srcImage, $srcWidth, $srcHeight, $newWidth, $newHeight): \GdImage|bool
    {
        $newWidth = (int)$newWidth;
        $newHeight = (int)$newHeight;
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resizedImage, false);
        $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
        imagefill($resizedImage, 0, 0, $transparent);
        imagesavealpha($resizedImage, true);
        imagesavealpha($resizedImage, true);
        imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

        return $resizedImage;
    }
}
