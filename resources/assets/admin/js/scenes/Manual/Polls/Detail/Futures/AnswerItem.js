import React, { Component } from 'react';
import { Formik } from 'formik';
import * as Yup from 'yup';
import {
  Card, Stack,
  Form, FormLayout,
  InlineError, TextField,
  Button, ButtonGroup, ResourceList, SkeletonBodyText,
  Checkbox, Autocomplete
} from '@shopify/polaris';
import Api from '../../../../../apis/app';
import OptionsHelper from '../../../../../helpers/OptionsHelper';

class AnswerItem extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isDeleting: false,
      isEditMode: false,
      showDeleteConfirm: false,
      candidates: [],
      options: [],
      selectedOptions: []
    };
    this.formikRef = null;
    this.handleEdit = this.handleEdit.bind(this);
    this.handleDelete = this.handleDelete.bind(this);
    this.toggleEditMode = this.toggleEditMode.bind(this);
    this.toggleDeleteConfirm = this.toggleDeleteConfirm.bind(this);
  }
  
  async componentDidMount() {
    const {
      body: candidates
    } = await Api.get('admin/manual/candidates/all', {category_id: this.props.page.category.id});
    this.setState({
      candidates: OptionsHelper.getOptions(candidates, 'id', 'name')
    });
  }

  setDeleting(isDeleting) {
    this.setState({
      isDeleting
    });
  }

  toggleEditMode() {
    this.setState({
      isEditMode: !this.state.isEditMode
    });
  }

  toggleDeleteConfirm() {
    this.setState({
      showDeleteConfirm: !this.state.showDeleteConfirm
    });
  }

  async handleEdit(values, bags) {
    const {
      onEdit, data, index
    } = this.props;
    await onEdit(data.id, values, bags, this, index);
  }

  async handleDelete() {
    const {
      onDelete, data, index
    } = this.props;
    await onDelete(data.id, this, index);
  }

  renderEditMode() {
    const {
      data
    } = this.props;
    const { candidates, options, selectedOptions } = this.state;
    return (
      <Formik
        ref={this.formikRef}
        initialValues={{
          name: data.candidate.name || '',
          score: data.score || 0,
          standing: data.standing || '',
          odds: data.odds || '',
          winning_points: data.winning_points || '',
          losing_points: data.losing_points || '',
          is_absent: data.is_absent || 0
        }}
        validationSchema={
          Yup.object().shape({
            name: Yup.string().required('Name is required!'),
            score: Yup.number(),
            odds: Yup.number(),
            winning_points: Yup.number(),
            losing_points: Yup.number()
          })
        }
        onSubmit={this.handleEdit}
        render={({
          values,
          errors,
          status,
          touched,
          setFieldValue,
          handleSubmit,
          handleBlur,
          isSubmitting
        }) => (
          <Card>
            <ResourceList.Item>
              <Form onSubmit={handleSubmit}>
                <FormLayout>
                  <FormLayout.Group>
                    {status && <InlineError message={status} />}
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Autocomplete
                      options={options}
                      selected={selectedOptions}
                      onSelect={(selected) => {
                        const selectedValue = selected.map((selectedItem) => {
                          const matchedOption = options.find((option) => {
                            return option.value.match(selectedItem);
                          });
                          return matchedOption && matchedOption.label;
                        });
                        this.setState({
                          selectedOptions: selected
                        });
                        
                        setFieldValue('name', selectedValue);
                      }}
                      textField={
                        <Autocomplete.TextField
                          onChange={(value) => {
                            setFieldValue('name', value)

                            if (value === '') {
                              this.setState({
                                options: candidates
                              });
                              return;
                            }

                            const filterRegex = new RegExp(value, 'i');
                            const resultOptions = candidates.filter((option) =>
                              option.label.match(filterRegex),
                            );
                            this.setState({
                              options: resultOptions
                            })
                          }}
                          label="Name"
                          value={values.name}
                          onBlur={handleBlur}
                          name="name"
                          error={touched.name && errors.name}
                        />
                      }
                    />
                    <TextField
                      label="Score"
                      placeholder="Score"
                      name="score"
                      value={values.score}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('score', value);
                      }}
                      error={touched.score && errors.score}
                    />
                    <TextField
                      label="Standing"
                      placeholder="Standing"
                      name="standing"
                      value={values.standing}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('standing', value);
                      }}
                      error={touched.standing && errors.standing}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <TextField
                      label="Odds"
                      placeholder="Odds"
                      name="odds"
                      value={values.odds}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('odds', value);
                      }}
                      error={touched.odds && errors.odds}
                    />
                    <TextField
                      label="Winning Points"
                      placeholder="Winning Points"
                      name="winning_points"
                      value={values.winning_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('winning_points', value);
                      }}
                      error={touched.winning_points && errors.winning_points}
                    />
                    <TextField
                      label="Losing Points"
                      placeholder="Losing Points"
                      name="losing_points"
                      value={values.losing_points}
                      onBlur={handleBlur}
                      onChange={(value) => {
                        setFieldValue('losing_points', value);
                      }}
                      error={touched.losing_points && errors.losing_points}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <Checkbox
                      label="Absent"
                      checked={values.is_absent === 1}
                      onChange={(value) => {
                        setFieldValue('is_absent', value === true ? 1 : 0);
                      }}
                    />
                  </FormLayout.Group>
                  <FormLayout.Group>
                    <ButtonGroup segmented>
                      <Button
                        loading={isSubmitting}
                        submit
                        primary
                        icon="checkmark"
                      />
                      <Button
                        onClick={this.toggleEditMode}
                        icon="cancelSmall"
                      />
                    </ButtonGroup>
                  </FormLayout.Group>
                </FormLayout>
              </Form>
            </ResourceList.Item>
          </Card>
        )}
      />
    );
  }

  renderViewMode() {
    const {
      data
    } = this.props;
    const {
      showDeleteConfirm, isDeleting
    } = this.state;

    return (
      <Card>
        <ResourceList.Item>
          {
            showDeleteConfirm ? (
              <Stack>
                <Stack.Item fill>
                  {`Do you want to delete this?`}
                </Stack.Item>
                <Stack.Item>
                  <ButtonGroup segmented>
                    <Button
                      primary
                      loading={isDeleting}
                      onClick={this.handleDelete}
                    >
                      Yes
                    </Button>
                    <Button
                      disabled={isDeleting}
                      onClick={this.toggleDeleteConfirm}
                    >
                      No
                    </Button>
                  </ButtonGroup>
                </Stack.Item>
              </Stack>
            ) : (
              <Stack>
                <Stack.Item fill>
                  <div style={{ width: '120px'}}>{data.candidate.name}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '80px'}}>{data.candidate.short_name}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '60px'}}>{data.score}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '100px'}}>{data.standing}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '60px'}}>{data.odds}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '100px'}}>{`+${data.winning_points ? data.winning_points : '0'} / ${data.losing_points ? data.losing_points : '0'}`}</div>
                </Stack.Item>
                <Stack.Item fill>
                  <div style={{ width: '60px'}}>{data.is_absent == 1 ? 'Absent' : ''}</div>
                </Stack.Item>
                <Stack.Item>
                  <div style={{ width: '80px' }}>
                    <ButtonGroup segmented>
                      <Button
                        primary
                        icon="products"
                        onClick={this.toggleEditMode}
                      />
                      <Button
                        destructive
                        icon="delete"
                        onClick={this.toggleDeleteConfirm}
                      />
                    </ButtonGroup>
                  </div>
                </Stack.Item>
              </Stack>
            )
          }
        </ResourceList.Item>
      </Card>
    );
  }

  render() {
    const {
      isLoading
    } = this.props;
    const {
      isEditMode
    } = this.state;
    if (isLoading) {
      return (
        <Card>
          <ResourceList.Item>
            <SkeletonBodyText />
          </ResourceList.Item>
        </Card>
      );
    }
    return isEditMode ? this.renderEditMode() : this.renderViewMode();
  }
}

AnswerItem.defaultProps = {
  isLoading: false,
  onEdit: () => {},
  onDelete: () => {}
  // onToggleActive: () => {}
};

export default AnswerItem;
