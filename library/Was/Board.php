<?php

class Was_Board {
    protected $_boardTable;
    protected $_boardReplyTable;
    protected $_fileTable;
    protected $_fileDetailsTable;
    protected $_validate;
    
    public function __construct() {
        $this->_boardTable = new Was_Board_Table_Board();
        $this->_boardReplyTable = new Was_Board_Table_BoardReply();
        $this->_fileTable = new Was_Board_Table_File();
        $this->_fileDetailsTable = new Was_Board_Table_FileDetails();
        $this->_validate = new Zend_Validate_NotEmpty();
    }
    //호출 확인용
    public function getBoardList() {
        return $this->_boardTable->fetchAll();
    }
    
    public function addBoard(array $datas) {
        if (!($this->_validate->isValid($datas['title'])) || !($this->_validate->isValid($datas['writer'])) || !($this->_validate->isValid($datas['content']))) {
            throw new Exception("게시글 등록 오류 확인 : 전달받은 값 에러! 부족한 값을 입력해주세요.");
        }
        $title = $this->_boardTable->getAdapter()->quote($datas['title']);
        $writer = $this->_boardTable->getAdapter()->quote($datas['writer']);
        $strip = $this->_boardTable->getAdapter()->quote(strip_tags($datas['content'], '<br>'));
        $insertData = array(
                'memberPk' => $datas['memberPk'],
                'title' => $title,
                'writer' => $writer,
                'content' => $strip,
                'insertTime' => new Zend_Db_Expr('NOW()'),
                'updateTime' => new Zend_Db_Expr('NOW()')
        );
        $result = $this->_boardTable->insert($insertData);
        return $result;
    }
    
    public function editBoard(array $datas) {
        if (!($this->_validate->isValid($datas['title'])) || !($this->_validate->isValid($datas['writer'])) || !($this->_validate->isValid($datas['content']))) {
            throw new Exception("게시글 수정 오류 확인 : 전달받은 값 에러! 부족한 값을 입력해주세요.");
        }
        // updateTime 수정
        $title = $this->_boardTable->getAdapter()->quote($datas['title']);
        $writer = $this->_boardTable->getAdapter()->quote($datas['writer']);
        $strip = $this->_boardTable->getAdapter()->quote(strip_tags($datas['content'], '<br>'));
        $updateData = array(
                'title' => $title,
                'writer' => $writer,
                'content' => $strip,
                'updateTime' => new Zend_Db_Expr('NOW()')
        );
        $where = $this->_boardTable->getAdapter()->quoteInto('pk = ?', $datas['no']);
        $result = $this->_boardTable->update($updateData, $where);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    public function delBoard($no) {
        $fileCheck = $this->_fileTable->select()->where('boardPk = ?',$no);
        $fileRowSet = $this->_fileTable->fetchAll($fileCheck);
        if (count($fileRowSet) > 0) {
            foreach ($fileRowSet as $value) {
                $this->delFile($value['pk']);
            }
        }
        $replyCheck = $this->_boardReplyTable->select()->where('pk = ?', $no);
        $replyRowSet = $this->_boardReplyTable->fetchAll($replyCheck);
        if (count($replyRowSet) > 0) {
            foreach ($replyRowSet as $value) {
                $this->delReply($value['pk']);
            }
        }
        $where = $this->_boardTable->getAdapter()->quoteInto('pk = ?', $no);
        $this->_boardTable->delete($where);
        return true;
    }
    
    public function getBoard($no) {
        $select = $this->_boardTable->select()->where('pk = ?', $no);
        return $this->_boardTable->fetchAll($select);
    }
    
    public function getAllBoard(array $conditions) {
        $select = $this->_boardTable->select()->where($conditions["searchTag"] . ' Like ("?")', new Zend_Db_Expr('%' . $conditions["searchInput"] . '%'))
                                              ->order($conditions["orderName"] . " " . $conditions["sort"])
                                              ->limit((($conditions['start_list'] - 1) * 10), 10);
        return $this->_boardTable->fetchAll($select);
    }
    
    public function addFile($boardPk, array $fileInfos) {
        $insertFile = array(
                'boardPk' => $datas['boardPk'],
                'filename' => $fileInfos['name'],
                'fileType' => explode('/', $fileInfos['type'])[1],
                'fileSize' => $fileInfos['size'],
                'insertTime' => new Zend_Db_Expr('NOW()')
        );
        $result = $this->_fileTable->insert($insertFile);
        
        //임시 파일 저장
        $filepath = APPLICATION_DATA;
        $filename = $filepath.iconv("UTF-8", "EUC-KR",$fileInfos['name']);
        move_uploaded_file($fileInfos['tmp_name'], $filename);
        //파일 내용 업로드
        $content = $this->_fileTable->getAdapter()->quote(file_get_contents($filename));
        
        $insertFileDetails = array(
                'filePk' => $result['pk'],
                'content' => $content
        );
        $result = $this->_fileDetailsTable->insert($insertFileDetails);
        //임시 파일 삭제
        unlink($filename);
        return $result;
    }
    
    public function getFiles($boardPk) {
        $select = $this->_fileTable->select()->where('boardPk = ?', $boardPk);
        $rows = $this->_fileTable->fetchAll($select);
        $resultArray = array();
        foreach ($rows as $value) {
            $filedata = $value;
            $select = $this->_fileDetailsTable->select()->from($this->_fileDetailsTable, array('content'))
                                                        ->where('filePk = ?', $value['pk']);
            $result = $this->_fileDetailsTable->fetchAll($select);
            $filedata['content'] = $result['content'];
            array_push($resultArray, $filedata);
        }
        return $resultArray;
    }
    
    public function delFile($filePk) {
        $this->_fileTable->delete($this->_fileTable->getAdapter()->quoteInto('pk = ?', $filePk));
        $this->_fileDetailsTable->delete($this->_fileDetailsTable->getAdapter()->quoteInto('filePk = ?', $filePk));
        return true;
    }
    
    public function addReply(array $datas) {
        $strip = $this->_boardReplyTable->getAdapter()->quote(strip_tags($datas['content'], '<br>'));
        
        $insertData = array(
                'boardPk' => $datas['boardPk'],
                'memberPk' => $datas['memberPk'],
                'content' => $strip,
                'insertTime' => new Zend_Db_Expr('NOW()')
        );
        $result = $this->_boardReplyTable->insert($insertData);
        return $result;
    }
    
    public function getReply($boardPk) {
        $this->_boardReplyTable->select()->setIntegrityCheck(false)
                                         ->from($this->_boardReplyTable, array('board_reply.*, member.name'))
                                         ->join('member', 'board_reply.memberPk = member.pk')
                                         ->where('board_reply.boardPk = ?', $boardPk)
                                         ->order('insertTime DESC');
        $rows = $this->_boardReplyTable->fetchAll($select);
        return $rows;
    }
    
    public function delReply($no) {
        $this->_boardReplyTable->delete($this->_boardReplyTable->getAdapter()->quoteInto('pk = ?', $no));
        return true;
    }
    
}