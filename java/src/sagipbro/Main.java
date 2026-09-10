package sagipbro;

public final class Main {
	private Main() {
	}

	public static void main(String[] args) {
		try (java.sql.Connection ignored = DatabaseConnection.open()) {
			System.out.println("SAGIPBRO database connection successful.");
		} catch (java.sql.SQLException exception) {
			System.err.println("SAGIPBRO database connection failed: " + exception.getMessage());
			System.exit(1);
		}
	}
}
