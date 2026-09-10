package sagipbro;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public final class DatabaseConnection {
	private static final String URL = System.getenv().getOrDefault("SAGIPBRO_DB_URL", "jdbc:mysql://localhost:3306/sagipbro_db?useSSL=false&serverTimezone=UTC");
	private static final String USER = System.getenv().getOrDefault("SAGIPBRO_DB_USER", "root");
	private static final String PASSWORD = System.getenv().getOrDefault("SAGIPBRO_DB_PASSWORD", "");

	private DatabaseConnection() {
	}

	public static Connection open() throws SQLException {
		return DriverManager.getConnection(URL, USER, PASSWORD);
	}
}
