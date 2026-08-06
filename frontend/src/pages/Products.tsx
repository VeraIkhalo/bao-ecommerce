import { useEffect, useState } from "react";
import api from "../api/api";
import type { Product } from "../types/Product";

export default function Products() {
    const [products, setProducts] = useState<Product[]>([]);

    useEffect(() => {
        const fetchProducts = async () => {
            const response = await api.get<Product[]>("/products");
            setProducts(response.data);
        };

        fetchProducts();
    }, []);

    return (
        <div>
            <h2>Products</h2>

            {products.map((product) => (
                <div key={product.id}>
                    <h3>{product.name}</h3>

                    <p>{product.description}</p>

                    <p>${product.price}</p>

                    <p>Stock: {product.stock}</p>

                    <button>Buy</button>
                </div>
            ))}
        </div>
    );
}