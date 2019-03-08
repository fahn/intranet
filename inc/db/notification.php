<?php



Class Notification {
    public $id;
    public $userId;
    public $created;
    public $text;
    public $isRead;

    public function __construct($dataSet = null) {
        if ($dataSet == null) {
            break;
        }

        $this->id      = $dataSet['id'];
        $this->userId  = $dataSet['userId'];
        $this->created = $dataSet['created'];
        $this->text    = $dataSet['text'];
        $this->isRead  = $dataSet['isRead'];
    }

    public function isRead() {
        return $this->isRead;
    }
}

?>
