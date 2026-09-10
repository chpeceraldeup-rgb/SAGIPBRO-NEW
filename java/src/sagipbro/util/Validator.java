package sagipbro.util;

public final class Validator {
	private Validator() {
	}

	public static String required(String value, String field) {
		if (value == null || value.trim().isEmpty()) {
			throw new IllegalArgumentException(field + " is required");
		}
		return value.trim();
	}

	public static int positive(int value, String field) {
		if (value < 1) {
			throw new IllegalArgumentException(field + " must be positive");
		}
		return value;
	}
}
