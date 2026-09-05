<?php

namespace App;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Sentinel;
use App\Register;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
     /**
     * @return \Illuminate\Support\Collection
     */

    protected $s;
    protected $e;

    public function __construct($s,$e)
    {
        $this->s = $s;
        $this->e = $e;
    }


    public function collection()
    {

        $s  = $this->s;
        $e  = $this->e;
        //dd($s,$e);

        //return Register::all();
        return Register::select("id","First_Name", "Last_Name", "Gender", "Day", "Month","Year","email","Mobile","Address","City","State","PinCode","Landmark","Shipping_Address","Shipping_City","Shipping_State","Shipping_PinCode","Shipping_Landmark","user_type","created_at")->whereBetween('created_at', [$s, $e])->orderBy('id', 'DESC')->get();

    }

    public function headings(): array
    {

        return ['#','First Name','Last Name','Gender','DOB Day','DOB Month','DOB Year','Email ID','Mobile','Address','City','State','PinCode','Landmark','Shipping Address','Shipping City','Shipping State','Shipping PinCode','Shipping Landmark','User Type','Date'];

    }

}
