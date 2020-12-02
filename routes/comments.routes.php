<?php
require_once('./database/connection.php');
require_once('./controllers/comments.controller.php');
require_once('./services/comment.service.php');
require_once('./middlewares/auth.php');


function commentsRoutes($method, $resource, $id)
{
    $commentService = new CommentService(connection());
    $commentsController = new CommentsController($commentService);

    switch ($method) {
        case 'GET': return $commentsController->index($resource, $id);
        case 'POST': {
            return $commentsController->store($resource, $id);
        };
        case 'DELETE': {
            if (!auth($id, false)) return;
            return $commentsController->delete($resource, $id);
        }
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: POST, PUT, DELETE, GET');
    }
}