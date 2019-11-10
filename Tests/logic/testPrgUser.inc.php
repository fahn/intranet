use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

public function testGetProjectInformation() {
    // Here, we create a mock prMysql object so we don't use the original
    $prMysql = $this->getMockBuilder(prMysql::class)
        ->disableOriginalConstructor()
        ->getMock();

    /* Here, we say that we expect a call to mysql_query with a given query,
     * and when we do, return a certain result.
     * You will also need to mock other methods as required */
    $expectedQuery = "SELECT product_id, projectName FROM test_projects
            WHERE projectId = 1";
    $returnValue = [['product_id' => 1, 'projectName' => 'test Name']];
    $prMysql->expects($this->once())
        ->method('query')
        ->with($this->equalTo($expectedQuery))
        ->willReturn($returnValue);

    // Here we call the method and do some checks on it
    $object = new ProjectHandler($prMysql);
    $result = $object->getProjectInformation(1);
    $this->assertSame($returnValue, $result);
}