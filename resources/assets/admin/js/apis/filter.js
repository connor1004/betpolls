const Filter = {
  filter: async (headers, response) => {
    const body = await response.json();
    return {
      response,
      body
    };
  }
};

export default Filter;
