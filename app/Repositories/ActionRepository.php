<?php
namespace App\Repositories;

use App\Models\Action;
use Illuminate\Http\Request;

class ActionRepository{

    public function actionList(Request $req){
        $actionList = Action::actionList($req);
        return $actionList;
    }

}







