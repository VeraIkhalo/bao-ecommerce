import { 
    BrowserRouter, 
    Routes, 
    Route 
} from "react-router-dom";

import { 
    lazy, 
    Suspense 
} from "react";

import ProtectedRoute from "./components/ProtectedRoute";
import DashboardLayout from "./layouts/DashboardLayout";


// Public pages
const Login = lazy(() => import("./pages/Login"));
const Register = lazy(() => import("./pages/Register"));


// Protected pages
const Dashboard = lazy(() => import("./pages/Dashboard"));
const Products = lazy(() => import("./pages/Products"));
const Orders = lazy(() => import("./pages/Orders"));
const Notifications = lazy(() => import("./pages/Notifications"));


// Error page
const NotFound = lazy(() => import("./pages/NotFound"));


function Loading() {
    return (
        <div className="flex items-center justify-center min-h-screen">
            Loading...
        </div>
    );
}


export default function App() {

    return (

        <BrowserRouter>

            <Suspense fallback={<Loading />}>

                <Routes>


                    {/* Public Routes */}

                    <Route 
                        path="/" 
                        element={<Login />} 
                    />


                    <Route 
                        path="/register" 
                        element={<Register />} 
                    />



                    {/* Protected Routes */}

                    <Route
                        element={
                            <ProtectedRoute>
                                <DashboardLayout />
                            </ProtectedRoute>
                        }
                    >

                        <Route
                            path="/dashboard"
                            element={<Dashboard />}
                        />


                        <Route
                            path="/products"
                            element={<Products />}
                        />


                        <Route
                            path="/orders"
                            element={<Orders />}
                        />


                        <Route
                            path="/notifications"
                            element={<Notifications />}
                        />


                    </Route>



                    {/* Catch unknown routes */}

                    <Route
                        path="*"
                        element={<NotFound />}
                    />


                </Routes>

            </Suspense>

        </BrowserRouter>

    );
}