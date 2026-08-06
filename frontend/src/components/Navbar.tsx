import { Link, useNavigate } from "react-router-dom";

export default function Navbar() {
    const navigate = useNavigate();

    const logout = () => {
        localStorage.clear();
        navigate("/");
    };

    return (
        <nav
            style={{
                display: "flex",
                gap: "20px",
                padding: "15px",
                background: "#eee",
            }}
        >
            <Link to="/dashboard">Dashboard</Link>

            <Link to="/products">Products</Link>

            <Link to="/orders">Orders</Link>

            <Link to="/notifications">Notifications</Link>

            <button onClick={logout}>Logout</button>
        </nav>
    );
}