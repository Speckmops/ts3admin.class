<?PHP

class Teamspeak_Connection
{
    private $Socket = null;
    private $Address = null;
    private $Port = 10011;
    private $Timeout = 2; //Seconds

    public function __construct($Address, $Port = 10011)
    {
        $this->Address = $Address;
        $this->Port = $Port;
    }

    public function Connect()
    {
        $errnum = 0;
        $errstr = null;

        $Socket = @fsockopen($this->Address, $this->Port, $errnum, $errstr, $this->Timeout);

        if($Socket === false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function SetTimeout($Timeout)
    {
        $this->Timeout = $Timeout;
    }
}

?>