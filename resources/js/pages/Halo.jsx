import React from 'react';

export default function Welcome() {
    return (
        <div style={{ display: 'flex', height: '100vh', alignItems: 'center', justifyContent: 'center', fontFamily: 'sans-serif' }}>
            <div style={{ textAlign: 'center' }}>
                <h1 style={{ color: '#2563eb', fontSize: '2.5rem' }}>Selamat Datang di KyoraPOS</h1>
                <p style={{ color: '#4b5563' }}>Sistem POS berbasis Laravel + React (Tanpa Docker)</p>
            </div>
        </div>
    );
}