<?php

//db 연결
$connect = mysql_connect("localhost", "root", "autoset");
$dbConn = mysql_select_db("test");

//boardId 받아오기
$boardId = $_GET['boardId'];

//글번호에 맞는 쿠키가 없을 때
if(!isset($_COOKIE[$boardId]) || $_COOKIE[$boardId] != TRUE) {
    setcookie($boardId, TRUE, time() + (60 * 60 * 24), '/');
    $q = "update board set hits = hits +1 where board_id = $boardId";
    if (!$result = mysql_query($q))
        echo "쿠키 생성 실패";
}

//boardId로 제목 레코드 추출
$q = "select subject from board where board_id = $boardId";
$result = mysql_query($q);
$title = mysql_result($result, 0);

//boardId로 내용 레코드 추출
$q = "select contents from board where board_id = $boardId";
$result = mysql_query($q);
$contents = mysql_result($result, 0);

$q = "select user_name from board where board_id = $boardId";
$userName = mysql_result(mysql_query($q), 0);
?>

<html>
<head>
    <style>

        table{
            margin-left: auto;
            margin-right: auto;
            margin-top: 50px;
            text-align: center;
            border-collapse: collapse;

        }
        table td {
            padding: 10px;
        }
        div {
            text-align: center;
        }
    </style>
</head>
<body>
    <script>
        //목록으로 돌아가는 함수
        function backList() {
            location.href = 'list.php';
        }

        function updateMode() {
            document.getElementById('mode').value = "update";
        }
        function deleteMode() {
            location.href = 'dbInsert.php?mode=delete&boardId=<? echo $boardId?>';
        }
        function blankCheck() {
            if(document.getElementById('comment').value.trim() == '')
                alert("댓글을 작성하세요.");
            else
                document.commentForm.submit();
        }
    </script>
    <form action="board.php" method="post">
        <table border="1">
            <tr>
                <td>제목</td>
                <td width="250px" height="50px"><? echo $title ?></td>
                <td>작성자</td>
                <td><? echo $userName ?></td>
            </tr>
            <tr>
                <td>내용</td>
                <td colspan="3" height="400px"><? echo $contents ?></td>
            </tr>
            <tr>
                <td colspan="4">
                    <input type="button" value="목록" onclick="backList()">
                    <input type="submit" id="updateBtn" value="수정" onclick="updateMode()">
                    <input type="button" id="deleteBtn" value="삭제" onclick="deleteMode()">
                </td>
            </tr>
        </table>

        <input type="hidden" name="title" value="<? echo $title ?>">
        <input id="mode" type="hidden" name="mode" value="">
        <input type="hidden" name="contents" value="<? echo $contents ?>">
        <input type="hidden" name="boardId" value="<? echo $boardId ?>">
    </form>
    <?
    //로그아웃 상태에서 수정, 삭제 버튼 숨김
    if(!isset($_SESSION['id'])){
        echo "<script>
            document.getElementById('updateBtn').type='hidden';
            document.getElementById('deleteBtn').type='hidden';
          </script>";
    }
    ?>
    <!--댓글창-->
    <form action="dbInsert.php" method="post" name="commentForm">
        <div>
            <br><br>
            <input type="text" name="comment" id="comment" style="width: 30%; height: 40px">
            <input type="button" onclick="blankCheck()" style="height: 40px" value="댓글">
            <input type="hidden" name="mode" value="comment">
            <input type="hidden" name="boardId" value="<? echo $boardId ?>">
        </div>
    </form>

    <table border="0">
        <?
        $q = "select * from board where board_pid = '$boardId' order by board_id desc";
        $num = mysql_num_rows(mysql_query($q));
        $result = mysql_query($q);
        for($i = 0; $i < $num; $i++){
            $row = mysql_fetch_array($result);
            echo "<tr style='border-top: dashed 1px ;'>";
            echo "<td><b>".$row['user_name']."</b></td>";
            echo "<td style='width: 80%'>".$row['contents']."</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
