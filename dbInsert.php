<html>
    <form action='dbInsert.php' method='post' name="uPassword">
        <input type='hidden' name='mode' value='delete'>
        <input type='hidden' name='boardId' value='' id="boardId">
        <input type='hidden' name='uPassword' value='' id="uPassword">
    </form>
</html>
<?php

//db 연결
$connect = mysql_connect("localhost", "root", "autoset");
$dbConn = mysql_select_db("test");

if(isset($_POST['boardId']))
    $boardId = $_POST['boardId'];
else
    $boardId = $_GET['boardId'];

if(isset($_POST['mode']))
    $mode = $_POST['mode'];
else
    $mode = $_GET['mode'];

//comment mode
if(isset($_POST['mode']) && $_POST['mode'] == 'comment'){
    //로그인 시 이름 = 닉네임
    if(isset($_SESSION['id'])){
        $userId = $_SESSION['id'];
        $userName = $_SESSION['name'];
    }else{
        //로그아웃 시 이름 = nonMember
        $userId = 'nonMember';
        $userName = 'nonMember';
    }

    $comment = $_POST['comment'];

    //db 레코드 삽입
    $q = "insert into board (board_pid, contents, reg_date, user_id, user_name)";
    $q.= "VALUES('$boardId', '$comment', now(), '$userId', '$userName')";

    if(mysql_query($q))
        echo "<script>location.href = 'post.php?boardId=$boardId';</script>";

}
//update mode
if($mode == 'update'){

    //db 레코드 삽입
    $q = "update board set subject = '".$_POST['title']."', contents = '".$_POST['contents']."'";
    $q .= "where board_id = $boardId";

    if($result = mysql_query($q))
        echo "<script>location.href = 'post.php?boardId=$boardId'</script>";
}

//delete mode
if($mode == 'delete') {
    //비밀번호 입력 전
    if (!isset($_POST['uPassword'])) {
        echo "<script>
            var uPassword = prompt('사용자의 비밀번호를 입력하세요.');
            document.getElementById('uPassword').value = uPassword;
            document.getElementById('boardId').value = $boardId;
            document.uPassword.submit();
          </script>";
    }
    //비밀번호 입력 후, 비밀번호가 일치하면
    if (isset($_POST['uPassword']) && $_POST['uPassword'] == $_SESSION['password']) {
        //db 레코드 삽입
        $q = "delete from board where board_id = " . $boardId;
        if ($result = mysql_query($q))
            echo "<script>location.href = 'list.php';</script>";
    }
    else
        //비밀번호가 맞지 않을 때
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";

}
//insert mode
if($mode == 'insert'){
    $userId = $_POST['userId'];
    $userName = $_POST['userName'];
    $title = $_POST['title'];
    $contents = $_POST['contents'];

    //db 레코드 삽입
    $q = "insert into board (subject, contents, reg_date, user_id, user_name)";
    $q.= "VALUES ('$title', '$contents', now(), '$userId', '$userName')";

    //작성 글이 db에 삽입되면
    if($result = mysql_query($q)){
        //작성된 글 확인
        $q = "select max(board_id) from board;";
        $postBoardId = mysql_result(mysql_query($q), 0);
        echo "<script>location.href = 'post.php?boardId=$postBoardId'</script>";
    }else{
        echo "작동x";
    }
}
?>
