<?php

echo "<script>if(!location.href.match('currPage')) location.href = 'list.php?currPage=1'</script>";

//db 연결
$connect = mysql_connect("localhost", "root", "autoset");
$dbConn = mysql_select_db("test");

//고정 색션
$section = 6;

//페이지 당 레코드 수
$pageRecordNum = 5;

//현재 페이지 값이 없으면 1페이지로 설정
if(isset($_GET['currPage']))
    $currPage = $_GET['currPage'];
else
    $currPage = 1;

//총 레코드 수
if(isset($_GET['select']) && isset($_GET['query'])){
    $select = $_GET['select'];
    $query = $_GET['query'];
    $q = "select * from board where board_pid = 0 and $select LIKE '%$query%' order by board_id desc";

}
else
    $q = "select * from board where board_pid = 0 order by board_id desc";

$allRecordNum = @mysql_num_rows(mysql_query($q));

//총 페이지 수 (페이지 당 레코드 수 - 3)
$allPageNum = ceil($allRecordNum / $pageRecordNum);

//현재 페이지 유효성 검사
if($currPage < 1)
    $currPage = 1;

if($currPage > $allPageNum)
    $currPage = $allPageNum;

//페이지 중간점
$middleNum = ceil($section/2);

//페이지에 따라 중간점 이동
if(($currPage - $middleNum) > 0 && ($currPage + ($middleNum -1) < $allPageNum)){
    $startPage = $currPage - ($middleNum -1);
    $lastPage = $startPage + ($section -1);
}else if(($currPage - $middleNum) <= 0){
    $startPage = 1;
    $lastPage = $startPage + ($section -1);
}else{
    $lastPage = $allPageNum;
    $startPage = $allPageNum - ($section -1);
}

//페이지 번호 유효성 검사
if($lastPage > $allPageNum)
    $lastPage = $allPageNum;

if($startPage < 1)
    $startPage = 1;

//페이지 레코드 추출
if($currPage != 1)
    $startRecord = $currPage * $pageRecordNum - $pageRecordNum;
else
    $startRecord = 0;

$q = $q." limit $startRecord, $pageRecordNum";
$result = mysql_query($q);

?>

<html>
<style>

    a:visited{
        color : darkred;
    }
    #pageNum a:hover{
        color : hotpink;
    }
    tbody a:hover{
        color : pink;
        text-decoration: underline;
    }
    div {
        text-align: center;
    }

    table {
        margin-left: auto;
        margin-right: auto;
        text-align: center;
        vertical-align: middle;
        border-collapse: collapse;
    }
    thead tr{
        background-color: #6a60a9;
        border-radius: 20px;
    }
    thead tr td {
        color: #fbd14b;
        font-weight: 600;
        height: 30px;
    }
    tbody tr td {
        width: 80px;
        height: 35px;
        border-bottom: solid 1px darkgray;
    }
    #writeBtn{
        margin-left: 930px;
    }
</style>
<body>

    <div>
        <h2>게시판☆</h2>
    </div>

    <div>
        <form id="loginForm" action="login.php" method="post" style="display: none">
            ID  <input name="id" type="text"> &nbsp;&nbsp;
            PW  <input name="password" type="password">
            <input type="submit" value="로그인">
        </form>
    </div>

    <?//로그인 상태일 때
    if(isset($_SESSION['id']) && !empty($_SESSION['id'])){
        echo "<div><b>".$_SESSION['name']."</b>님 환영합니다.  ";
        echo "<input type='button' value='로그아웃' onclick='loginOut()'></div>";
    }//로그아웃 상태일 때
    else{
        echo"<script>document.getElementById('loginForm').style.display = '';</script>";
    }
    ?>
    <hr>
    <!-- 검색창 -->
    <div align="float">
        <select id="select">
            <option>제목</option>
            <option>내용</option>
            <option>작성자</option>
        </select>
        <input type="text" id="query">
        <input type="button" value="검색" onclick="search()">
    </div><br>
    <table>
        <thead>
            <tr>
                <td>번호</td>
                <td style="width: 200px">제목</td>
                <td>작성자</td>
                <td>조회수</td>
                <td style="width: 150px;">작성일</td>
            </tr>
        </thead>
        <tbody>
        <?
            while($row =  @mysql_fetch_array($result)){
                $boardId = $row['board_id'];
                echo "<tr>";
                echo "<td>".$row['board_id']."</td>";
                echo "<td><a onclick='movePost($boardId)' style='cursor: pointer'>".$row['subject']."</a></td>";
                echo "<td>".$row['user_id']."</td>";
                echo "<td>".$row['hits']."</td>";
                echo "<td>".date("Y-m-d",strtotime($row['reg_date']))."</td>";
                echo "</tr>";
            }
        ?>
        </tbody>
    </table>
    <br>
    <input id="writeBtn" type="button" value="글쓰기" onclick="goBoard()">

    <div id="pageNum">
        <a onclick="backPage()"> << </a>
        <?
        for($i = $startPage; $i <= $lastPage; $i++){
            echo " "."<a onclick='page($i)' style='cursor: pointer'>$i</a>"." ";
        }
        ?>
        <a onclick="frontPage()"> >> </a>
    </div>



<script>
    //현재 페이지 번호
    var currPage = <? echo $currPage ?>;

    //누른 페이지 이동
    function page(movePage) {
        if(location.href.match('currPage'))
            location.href = document.location.href.replace(currPage, movePage);
    }
    //페이지 뒤로 가기
    function backPage() {
        if(currPage !== 1)
            location.href = document.location.href.replace(currPage, currPage-1);
    }
    //페이지 앞으로 가기
    function frontPage() {
        if(currPage !== <?echo $allPageNum?>)
            location.href = document.location.href.replace(currPage, currPage+1);
    }
    //게시판 검색
    function search() {
        var select = document.getElementById('select').value;

        if(select == '제목')
            select = 'subject';
        else if(select == '내용')
            select = 'contents';
        else
            select = 'user_id';

        var query = document.getElementById('query').value;
        location.href = 'list.php?currPage='+currPage+'&select='+select+'&query='+query;

        }
        //글작성 페이지 이동
        function goBoard() {
            location.href = 'board.php';
        }
        //글보기
        function movePost(boardId) {
            location.href = 'post.php?boardId=' + boardId;
        }

        function loginOut() {
            location.href = 'login.php';
        }
</script>
</body>
</html>