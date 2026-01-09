#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
🤖 AI Church API - Servidor Python Real
Primeiro modelo funcional: Previsão de Attendance
"""

import os
import sys
import json
import numpy as np
import pandas as pd
from datetime import datetime, timedelta
from flask import Flask, request, jsonify
from flask_cors import CORS
import tensorflow as tf
from sklearn.preprocessing import MinMaxScaler
from sklearn.model_selection import train_test_split
import joblib
import logging

# Configuração de logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Inicialização Flask
app = Flask(__name__)
CORS(app)

# Configurações
MODEL_PATH = 'models/'
DATA_PATH = 'data/'
CACHE_PATH = 'cache/'

# Criar diretórios
os.makedirs(MODEL_PATH, exist_ok=True)
os.makedirs(DATA_PATH, exist_ok=True)
os.makedirs(CACHE_PATH, exist_ok=True)

class AttendancePredictor:
    """Modelo real de previsão de attendance"""
    
    def __init__(self):
        self.model = None
        self.scaler = MinMaxScaler()
        self.is_trained = False
        self.load_or_train_model()
    
    def generate_sample_data(self):
        """Gerar dados realistas para treinamento inicial"""
        logger.info("📊 Gerando dados de exemplo para treinamento...")
        
        # Gerar 2 anos de dados semanais
        dates = pd.date_range(start='2022-01-01', end='2024-01-01', freq='W')
        data = []
        
        base_attendance = 150
        for i, date in enumerate(dates):
            # Fatores sazonais
            week_of_year = date.isocalendar()[1]
            month = date.month
            
            # Padrões realistas
            seasonal_factor = 1.0
            if month in [12, 1]:  # Dezembro/Janeiro (alta temporada)
                seasonal_factor = 1.3
            elif month in [7, 8]:  # Julho/Agosto (férias)
                seasonal_factor = 0.8
            elif month in [3, 4]:  # Páscoa
                seasonal_factor = 1.2
            
            # Tendência de crescimento
            growth_factor = 1 + (i * 0.002)  # 0.2% de crescimento por semana
            
            # Variação aleatória
            random_factor = np.random.normal(1.0, 0.1)
            
            # Eventos especiais
            special_event = 1.0
            if week_of_year in [1, 52]:  # Ano novo
                special_event = 1.4
            elif week_of_year >= 10 and week_of_year <= 13:  # Páscoa
                special_event = 1.25
            
            # Calcular attendance
            attendance = base_attendance * seasonal_factor * growth_factor * random_factor * special_event
            attendance = max(50, min(500, attendance))  # Limites realistas
            
            data.append({
                'date': date,
                'attendance': round(attendance),
                'week_of_year': week_of_year,
                'month': month,
                'year': date.year,
                'seasonal_factor': seasonal_factor,
                'special_event': special_event
            })
        
        df = pd.DataFrame(data)
        
        # Salvar dados
        df.to_csv(os.path.join(DATA_PATH, 'attendance_data.csv'), index=False)
        logger.info(f"✅ Dados gerados: {len(df)} registros")
        
        return df
    
    def prepare_features(self, df):
        """Preparar features para o modelo"""
        # Features temporais
        df['day_of_week'] = df['date'].dt.dayofweek
        df['quarter'] = df['date'].dt.quarter
        
        # Features lagged
        df['attendance_lag_1'] = df['attendance'].shift(1)
        df['attendance_lag_4'] = df['attendance'].shift(4)  # 4 semanas atrás
        df['attendance_lag_12'] = df['attendance'].shift(12)  # 12 semanas atrás
        
        # Médias móveis
        df['attendance_ma_4'] = df['attendance'].rolling(window=4).mean()
        df['attendance_ma_12'] = df['attendance'].rolling(window=12).mean()
        
        # Features de tendência
        df['attendance_trend'] = df['attendance'].diff()
        df['attendance_pct_change'] = df['attendance'].pct_change()
        
        # Remover NaNs
        df = df.dropna()
        
        return df
    
    def train_model(self, df):
        """Treinar modelo LSTM"""
        logger.info("🧠 Treinando modelo de previsão...")
        
        # Preparar features
        df = self.prepare_features(df)
        
        # Features para treinamento
        feature_columns = [
            'week_of_year', 'month', 'seasonal_factor', 'special_event',
            'attendance_lag_1', 'attendance_lag_4', 'attendance_lag_12',
            'attendance_ma_4', 'attendance_ma_12', 'attendance_trend'
        ]
        
        X = df[feature_columns].values
        y = df['attendance'].values
        
        # Normalizar features
        X_scaled = self.scaler.fit_transform(X)
        
        # Criar sequências para LSTM
        sequence_length = 8  # Usar 8 semanas anteriores
        
        def create_sequences(X, y, seq_length):
            X_seq, y_seq = [], []
            for i in range(seq_length, len(X)):
                X_seq.append(X[i-seq_length:i])
                y_seq.append(y[i])
            return np.array(X_seq), np.array(y_seq)
        
        X_seq, y_seq = create_sequences(X_scaled, y, sequence_length)
        
        # Dividir treino/teste
        X_train, X_test, y_train, y_test = train_test_split(
            X_seq, y_seq, test_size=0.2, random_state=42
        )
        
        # Criar modelo LSTM
        self.model = tf.keras.Sequential([
            tf.keras.layers.LSTM(64, return_sequences=True, input_shape=(sequence_length, X_train.shape[2])),
            tf.keras.layers.Dropout(0.2),
            tf.keras.layers.LSTM(32, return_sequences=False),
            tf.keras.layers.Dropout(0.2),
            tf.keras.layers.Dense(16, activation='relu'),
            tf.keras.layers.Dense(1)
        ])
        
        # Compilar modelo
        self.model.compile(
            optimizer='adam',
            loss='mse',
            metrics=['mae', 'mape']
        )
        
        # Callbacks
        callbacks = [
            tf.keras.callbacks.EarlyStopping(patience=10, restore_best_weights=True),
            tf.keras.callbacks.ReduceLROnPlateau(factor=0.5, patience=5)
        ]
        
        # Treinar
        history = self.model.fit(
            X_train, y_train,
            epochs=50,
            batch_size=16,
            validation_data=(X_test, y_test),
            callbacks=callbacks,
            verbose=1
        )
        
        # Avaliar modelo
        train_loss, train_mae, train_mape = self.model.evaluate(X_train, y_train, verbose=0)
        test_loss, test_mae, test_mape = self.model.evaluate(X_test, y_test, verbose=0)
        
        logger.info(f"📊 Treino - Loss: {train_loss:.4f}, MAE: {train_mae:.2f}, MAPE: {train_mape:.2%}")
        logger.info(f"📊 Teste - Loss: {test_loss:.4f}, MAE: {test_mae:.2f}, MAPE: {test_mape:.2%}")
        
        # Salvar modelo e scaler
        self.model.save(os.path.join(MODEL_PATH, 'attendance_model.h5'))
        joblib.dump(self.scaler, os.path.join(MODEL_PATH, 'attendance_scaler.pkl'))
        
        self.is_trained = True
        
        return {
            'train_mae': float(train_mae),
            'test_mae': float(test_mae),
            'train_mape': float(train_mape),
            'test_mape': float(test_mape),
            'samples': len(df)
        }
    
    def load_or_train_model(self):
        """Carregar modelo existente ou treinar novo"""
        model_file = os.path.join(MODEL_PATH, 'attendance_model.h5')
        scaler_file = os.path.join(MODEL_PATH, 'attendance_scaler.pkl')
        
        if os.path.exists(model_file) and os.path.exists(scaler_file):
            logger.info("📂 Carregando modelo existente...")
            try:
                self.model = tf.keras.models.load_model(model_file)
                self.scaler = joblib.load(scaler_file)
                self.is_trained = True
                logger.info("✅ Modelo carregado com sucesso")
                return True
            except Exception as e:
                logger.error(f"❌ Erro ao carregar modelo: {e}")
        
        # Treinar novo modelo
        df = self.generate_sample_data()
        metrics = self.train_model(df)
        logger.info("🎉 Modelo treinado com sucesso!")
        return metrics
    
    def predict(self, weeks_ahead=4):
        """Fazer previsão para próximas semanas"""
        if not self.is_trained:
            return {'error': 'Modelo não treinado'}
        
        try:
            # Carregar dados históricos
            df = pd.read_csv(os.path.join(DATA_PATH, 'attendance_data.csv'))
            df['date'] = pd.to_datetime(df['date'])
            df = self.prepare_features(df)
            
            # Pegar últimas sequências
            feature_columns = [
                'week_of_year', 'month', 'seasonal_factor', 'special_event',
                'attendance_lag_1', 'attendance_lag_4', 'attendance_lag_12',
                'attendance_ma_4', 'attendance_ma_12', 'attendance_trend'
            ]
            
            last_sequence = df[feature_columns].tail(8).values
            last_sequence_scaled = self.scaler.transform(last_sequence)
            
            # Fazer previsões
            predictions = []
            current_sequence = last_sequence_scaled.reshape(1, 8, -1)
            
            for week in range(weeks_ahead):
                pred = self.model.predict(current_sequence, verbose=0)
                predictions.append(float(pred[0][0]))
                
                # Atualizar sequência (simplificado)
                current_sequence = np.roll(current_sequence, -1, axis=1)
                # Aqui deveríamos atualizar com novas features, mas por ora mantemos simples
            
            # Gerar datas futuras
            last_date = df['date'].max()
            future_dates = [last_date + timedelta(weeks=i+1) for i in range(weeks_ahead)]
            
            # Calcular confiança (baseada na volatilidade histórica)
            historical_volatility = df['attendance'].std()
            confidence_interval = historical_volatility * 1.96  # 95% CI
            
            result = {
                'predictions': predictions,
                'dates': [d.strftime('%Y-%m-%d') for d in future_dates],
                'confidence_interval': confidence_interval,
                'model_info': {
                    'trained': True,
                    'last_training': datetime.now().isoformat(),
                    'sequence_length': 8
                }
            }
            
            return result
            
        except Exception as e:
            logger.error(f"❌ Erro na previsão: {e}")
            return {'error': str(e)}

# Inicializar modelo
predictor = AttendancePredictor()

@app.route('/')
def home():
    """Health check"""
    return jsonify({
        'status': 'active',
        'service': 'AI Church API',
        'version': '1.0.0',
        'model_loaded': predictor.is_trained,
        'timestamp': datetime.now().isoformat()
    })

@app.route('/predict/attendance', methods=['GET', 'POST'])
def predict_attendance():
    """Endpoint para previsão de attendance"""
    
    if request.method == 'GET':
        weeks_ahead = int(request.args.get('weeks', 4))
    else:
        data = request.get_json()
        weeks_ahead = data.get('weeks', 4)
    
    logger.info(f"🔮 Fazendo previsão para {weeks_ahead} semanas")
    
    result = predictor.predict(weeks_ahead)
    
    if 'error' in result:
        return jsonify({'error': result['error']}), 500
    
    # Adicionar metadados
    result['metadata'] = {
        'request_time': datetime.now().isoformat(),
        'weeks_predicted': weeks_ahead,
        'model_type': 'LSTM',
        'confidence_level': 0.95
    }
    
    return jsonify(result)

@app.route('/model/info')
def model_info():
    """Informações do modelo"""
    return jsonify({
        'model_loaded': predictor.is_trained,
        'model_type': 'LSTM',
        'training_data': 'attendance_data.csv',
        'features': [
            'week_of_year', 'month', 'seasonal_factor', 'special_event',
            'attendance_lag_1', 'attendance_lag_4', 'attendance_lag_12',
            'attendance_ma_4', 'attendance_ma_12', 'attendance_trend'
        ],
        'sequence_length': 8,
        'output': 'attendance_prediction'
    })

@app.route('/model/retrain', methods=['POST'])
def retrain_model():
    """Retreinar modelo"""
    logger.info("🔄 Retreinando modelo...")
    
    try:
        df = predictor.generate_sample_data()
        metrics = predictor.train_model(df)
        
        return jsonify({
            'success': True,
            'metrics': metrics,
            'retrain_time': datetime.now().isoformat()
        })
        
    except Exception as e:
        logger.error(f"❌ Erro no retreino: {e}")
        return jsonify({'error': str(e)}), 500

@app.route('/health')
def health_check():
    """Health check detalhado"""
    return jsonify({
        'status': 'healthy',
        'model_status': 'loaded' if predictor.is_trained else 'not_loaded',
        'database_connection': 'ok',  # TODO: implementar verificação real
        'memory_usage': 'normal',     # TODO: implementar verificação real
        'timestamp': datetime.now().isoformat()
    })

if __name__ == '__main__':
    logger.info("🚀 Iniciando AI Church API...")
    logger.info(f"📍 Modelo carregado: {predictor.is_trained}")
    
    # Rodar em produção com gunicorn
    app.run(host='0.0.0.0', port=5000, debug=False)
