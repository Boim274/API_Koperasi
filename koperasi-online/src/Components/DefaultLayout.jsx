import { useContext } from "react"; 
import { Navigate, Outlet } from "react-router-dom";
import { useStateContext } from "../contexts/contextprovider";
export default function Defaultlayout() {
    const {user,token} = useStateContext();
    if(!token){
        return <Navigate to='/login' />
    }

    return (
        <div id="defaultLayout">
        <div className="content">
            <header>
                <div>
                    Header
                </div>
                <div>
                    User Info
                </div>
            </header>
        </div>
        <Outlet />
    </div>
    )
}