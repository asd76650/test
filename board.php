<html>
<body>
    <form method="post" action="dbInsert.php" id="dbInsert" name="dbInsert">
        <table border="1" id="table">
            <tr>
                <td>제목</td>
                <td><input id="title" type="text" name="title"></td>
            </tr>
            <tr>
                <td>내용</td>
                <td><textarea id="contents" name="contents"></textarea></td>
            </tr>
            <tr>
                <td>
                    <input type="button" value="작성" onclick="writePost()">
                    <input type="button" value="취소" onclick="cancel()">
                </td>
            </tr>
        </table>
        <input id="mode" type="hidden" name="mode" value="">
        <input type="hidden" name="boardId" value="<? echo $_POST['boardId']?>">
        <input type="hidden" name="userName" value="<?
        //로그인 시 작성자 = 닉네임,
            if(isset($_SESSION['id']) && !empty($_SESSION['id']))
                echo $_SESSION['nickName'];
            else
                echo "nonMember";
        ?>">
        <input type="hidden" name="userId" value="<?
            if(isset($_SESSION['nickName']) && !empty($_SESSION['nickName']))
                echo $_SESSION['nickName'];
            else
                echo "nonMember";
        ?>">
    </form>
</body>
<script>

    var titleObj = document.getElementById('title');
    var contentsObj = document.getElementById('contents');
    var mode = document.getElementById('mode');

    //글 작성 취소
    function cancel() {
        location.href = 'list.php';
    }

    //글 수정
    function updateMode() {
        titleObj.value = '<? echo $_POST['title']?>';
        contentsObj.value = '<? echo $_POST['contents']?>';

        mode.value = 'update';
    }
    //제목, 글 공백 체크
    function writePost() {
        mode.value = 'insert';

        if(titleObj.value.trim() == '' || contentsObj.value.trim().length == 0)
            alert("제목 또는 글을 작성하세요.");
        else
            document.dbInsert.submit();

    }
</script>
</html>
<?
if(!(isset($_SESSION['id']) && !empty($_SESSION['id']))){
    echo "<script>alert('로그인 후 가능합니다.');</script>";
    echo "<script>location.href = 'list.php'</script>";
}

if(isset($_POST['mode']) && $_POST['mode'] == 'update')
    echo "<script>updateMode()</script>";


?>
