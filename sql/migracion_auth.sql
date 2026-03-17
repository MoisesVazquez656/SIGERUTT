-- =============================================
-- SIGERUTT - Migración: Sistema de Autenticación
-- Ejecutar sobre la base de datos `sistema_rutas`
-- =============================================

USE sistema_rutas;

ALTER TABLE usuarios
  ADD COLUMN session_id VARCHAR(128) DEFAULT NULL,
  ADD COLUMN token_recuperacion VARCHAR(64) DEFAULT NULL,
  ADD COLUMN token_expira DATETIME DEFAULT NULL;
