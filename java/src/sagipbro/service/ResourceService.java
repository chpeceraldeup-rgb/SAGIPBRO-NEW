package sagipbro.service;

import sagipbro.DatabaseConnection;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

public class ResourceService {
	public int availableStock(int resourceId) throws SQLException {
		String sql = "SELECT stock FROM resources WHERE id = ? AND status = 'Available'";
		try (Connection connection = DatabaseConnection.open(); PreparedStatement statement = connection.prepareStatement(sql)) {
			statement.setInt(1, resourceId);
			try (ResultSet result = statement.executeQuery()) {
				if (!result.next()) {
					throw new SQLException("Resource not found");
				}
				return result.getInt("stock");
			}
		}
	}
}
