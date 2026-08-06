import { Outlet } from "react-router-dom";
import Navbar from "../components/Navbar";

export default function DashboardLayout() {
    return (
        <>
            <Navbar />
            <main style={{ padding: "20px" }}>
                <Outlet />
            </main>
        </>
    );
}