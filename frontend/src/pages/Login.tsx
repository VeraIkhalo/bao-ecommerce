import { useState } from "react";
import api from "../api/api";
import { useNavigate } from "react-router-dom";
import type { User } from "../types/User";

interface LoginResponse {
    token: string;
    user: User;
}

export default function Login() {
    const navigate = useNavigate();

    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");

    const login = async () => {
        const response = await api.post<LoginResponse>("/login", {
            email,
            password,
        });

        localStorage.setItem("token", response.data.token);
        localStorage.setItem("user", JSON.stringify(response.data.user));

        navigate("/dashboard");
    };

    return (
        <div>
            <h2>Login</h2>

            <input
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Email"
            />

            <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Password"
            />

            <button onClick={login}>Login</button>
        </div>
    );
}